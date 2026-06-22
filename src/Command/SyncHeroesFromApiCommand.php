<?php

namespace App\Command;

use App\Entity\Affinity;
use App\Entity\Allegiance;
use App\Entity\Buffs;
use App\Entity\Debuffs;
use App\Entity\Disable;
use App\Entity\Faction;
use App\Entity\Heroes;
use App\Entity\Rarity;
use App\Entity\Type;
use App\Entity\Leader;
use App\Service\SlugService;
use App\Twig\SkillExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:sync-heroes-api',
    description: 'Synchronise les héros depuis l\'API ravenpyros.com (incrémental par défaut)',
)]
class SyncHeroesFromApiCommand extends Command
{
    use CommandGuardTrait;

    private const API_BASE      = 'https://www.ravenpyros.com/api/public/v1';
    private const STATE_FILE    = '/var/ravenpyros_sync_state.json';
    private const BATCH_SIZE    = 50;

    public function __construct(
        private EntityManagerInterface $em,
        private SlugService $slugService,
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
        #[Autowire('%env(RAVENPYROS_API_KEY)%')] private string $apiKey,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simule sans modifier la DB')
            ->addOption('force',   null, InputOption::VALUE_NONE, 'Ignore la version stockée et resynchronise tous les héros');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $force  = $input->getOption('force');

        if (!$this->acquireLock($this->getName())) {
            $io->error('La commande est déjà en cours d\'exécution. Abandon pour éviter les conflits.');
            return Command::FAILURE;
        }
        $this->applyResourceLimits();

        // Charge l'état de la dernière sync
        $stateFile     = $this->projectDir . self::STATE_FILE;
        $state         = file_exists($stateFile) ? (json_decode(file_get_contents($stateFile), true) ?? []) : [];
        $clientVersion = (!$force && isset($state['version'])) ? (int) $state['version'] : null;

        $io->title('Synchronisation des héros — API ravenpyros.com');

        if ($clientVersion) {
            $io->note(sprintf('Sync incrémentale depuis la version %d (dernière sync : %s)', $clientVersion, $state['synced_at'] ?? '?'));
        } else {
            $io->note('Sync complète — aucune version locale stockée');
        }

        // Un seul appel API pour récupérer tous les héros modifiés avec leurs détails complets
        $url = self::API_BASE . '/heroes?details=true';
        if ($clientVersion) {
            $url .= '&client_version=' . $clientVersion;
        }

        $io->writeln('Appel API…');
        $response = $this->apiGet($url);

        if ($response === null) {
            $io->error('Échec de l\'appel API. Vérifiez votre clé RAVENPYROS_API_KEY et votre connexion.');
            return Command::FAILURE;
        }

        $newVersion = $response['version'] ?? null;
        $heroList   = $response['data'] ?? [];

        $io->success(sprintf('%d héros à traiter (version API : %s)', count($heroList), $newVersion ?? '?'));

        if (empty($heroList)) {
            $io->writeln('<info>Tout est déjà à jour.</info>');
            return Command::SUCCESS;
        }

        if ($dryRun) {
            foreach ($heroList as $heroData) {
                $io->writeln(sprintf('  <info>[DRY-RUN]</info> %s (%s / %s)', $heroData['name'] ?? '?', $heroData['faction'] ?? '?', $heroData['rarity'] ?? '?'));
            }
            $io->note(sprintf('Dry-run terminé — %d héros seraient synchronisés.', count($heroList)));
            return Command::SUCCESS;
        }

        // Garantit que toutes les entités buff/debuff/disable de la constante existent en DB
        $this->seedAllEffects();

        // Pré-charge les entités de référence pour éviter des requêtes en boucle
        $factions    = $this->indexByName($this->em->getRepository(Faction::class)->findAll());
        $rarities    = $this->indexByName($this->em->getRepository(Rarity::class)->findAll());
        $affinities  = $this->indexByName($this->em->getRepository(Affinity::class)->findAll());
        $allegiances = $this->indexByName($this->em->getRepository(Allegiance::class)->findAll());
        $types       = $this->indexByName($this->em->getRepository(Type::class)->findAll());

        $created = 0;
        $updated = 0;
        $errors  = [];
        $batch   = 0;

        foreach ($heroList as $heroData) {
            $heroName = $heroData['name'] ?? null;
            if (!$heroName) {
                continue;
            }

            try {
                $hero  = $this->em->getRepository(Heroes::class)->findOneBy(['Name' => $heroName]);
                $isNew = $hero === null;

                if ($isNew) {
                    $hero = new Heroes();
                    $hero->setName($heroName);
                    $hero->setSlug($this->slugService->uniqueHeroSlug($heroName, null));
                }

                $this->applyBaseFields($hero, $heroData, $factions, $rarities, $affinities, $allegiances, $types);

                if ($isNew) {
                    $this->em->persist($hero);
                    $created++;
                    $io->writeln(sprintf('  <comment>+</comment> %s <comment>[nouveau]</comment>', $heroName));
                } else {
                    $updated++;
                    $io->writeln(sprintf('  <info>✓</info> %s', $heroName));
                }

            } catch (\Throwable $e) {
                $errors[] = sprintf('%s : %s', $heroName, $e->getMessage());
                $io->writeln(sprintf('  <error>✗ %s — %s</error>', $heroName, $e->getMessage()));
            }

            if (!$dryRun && ++$batch % self::BATCH_SIZE === 0) {
                $this->em->flush();
                $io->writeln(sprintf('  <comment>… flush intermédiaire (%d héros)</comment>', $batch));
            }
        }

        if (!$dryRun) {
            $this->em->flush();

            // Sauvegarde la nouvelle version pour la prochaine sync incrémentale
            if ($newVersion) {
                file_put_contents($stateFile, json_encode([
                    'version'   => $newVersion,
                    'synced_at' => (new \DateTimeImmutable())->format(\DateTime::ATOM),
                    'created'   => $created,
                    'updated'   => $updated,
                ], JSON_PRETTY_PRINT));
            }
        }

        $io->newLine();
        $io->success(sprintf('%d créés, %d mis à jour, %d erreurs', $created, $updated, count($errors)));

        if ($errors) {
            $io->section('Erreurs');
            $io->listing($errors);
        }

        if (!$dryRun && ($created > 0)) {
            $io->note('Nouveaux héros détectés — lance aussi : php bin/console app:scrape-hero-images');
        }

        return Command::SUCCESS;
    }

    /**
     * Met à jour uniquement les champs "source" du héros depuis l'API.
     * Les champs éditoriaux (weapons, armor, sets, imprints, buffs, debuffs,
     * disables, instants, videosUrl) ne sont jamais touchés.
     */
    private function applyBaseFields(
        Heroes $hero,
        array $data,
        array &$factions,
        array &$rarities,
        array &$affinities,
        array &$allegiances,
        array &$types,
    ): void {
        $hero->setName($data['name']);

        // Relations taxonomiques — crées à la volée si inexistantes, icônes stockées depuis l'API
        if ($faction = $data['faction'] ?? null) {
            $e = $factions[$faction] ?? $this->createEntity(Faction::class, $faction, $factions);
            if ($data['faction_icon'] ?? null) $e->setImageUrl($data['faction_icon']);
            $hero->setFactionEntity($e);
        }
        if ($rarity = $data['rarity'] ?? null) {
            $e = $rarities[$rarity] ?? $this->createEntity(Rarity::class, $rarity, $rarities);
            $hero->setRarityEntity($e);
        }
        if ($affinity = $data['affinity'] ?? null) {
            $e = $affinities[$affinity] ?? $this->createEntity(Affinity::class, $affinity, $affinities);
            if ($data['affinity_icon'] ?? null) $e->setImageUrl($data['affinity_icon']);
            $hero->setAffinityEntity($e);
        }
        if ($allegiance = $data['allegiance'] ?? null) {
            $e = $allegiances[$allegiance] ?? $this->createEntity(Allegiance::class, $allegiance, $allegiances);
            if ($data['allegiance_icon'] ?? null) $e->setImageUrl($data['allegiance_icon']);
            $hero->setAllegianceEntity($e);
        }
        if ($archetype = $data['archetype'] ?? null) {
            $e = $types[$archetype] ?? $this->createEntity(Type::class, $archetype, $types);
            if ($data['archetype_icon'] ?? null) $e->setImageUrl($data['archetype_icon']);
            $hero->setTypeEntity($e);
        }

        // Portrait (URL distante — app:scrape-hero-images téléchargera ensuite)
        if ($portraitUrl = $data['portrait_url'] ?? null) {
            $hero->setImageUrl($portraitUrl);
        }

        // Skills : on stocke "Nom|||Description|||iconUrl" (3 segments)
        $skills = $data['skills'] ?? [];
        foreach ($skills as $skill) {
            $type   = ucfirst(strtolower($skill['type'] ?? ''));
            $cooldown = isset($skill['cooldown']) && $skill['cooldown'] !== null ? (string) $skill['cooldown'] : '';
            $packed = ($skill['name'] ?? '') . '|||' . ($skill['description'] ?? '') . '|||' . ($skill['icon_url'] ?? '') . '|||' . $cooldown;
            match ($type) {
                'Basic'    => $hero->setBase($packed),
                'Core'     => $hero->setCore($packed),
                'Ultimate' => $hero->setUltimate($packed),
                'Passive'  => $hero->setPassive($packed),
                default    => null,
            };
        }

        // Stats de base
        $stats = $data['stats'] ?? [];
        if (isset($stats['hp']))   $hero->setStatHp((int) $stats['hp']);
        if (isset($stats['atk']))  $hero->setStatAtk((int) $stats['atk']);
        if (isset($stats['def']))  $hero->setStatDef((int) $stats['def']);
        if (isset($stats['spd']))  $hero->setStatSpd((int) $stats['spd']);
        if (isset($stats['init'])) $hero->setStatInit((int) $stats['init']);
        if (isset($stats['acc']))  $hero->setStatAcc((int) $stats['acc']);
        if (isset($stats['res']))  $hero->setStatRes((int) $stats['res']);

        // Leader Bonus
        $this->syncLeaderBonus($hero, $skills);

        // Buffs / Debuffs / Disables — déduits des effets affichés des skills
        $this->syncEffects($hero, $skills);

        // Divinity
        if (!empty($stats['initial_divinity'])) {
            $hero->setInitialDivinity((string) $stats['initial_divinity']);
        }
        foreach ($skills as $skill) {
            if (ucfirst(strtolower($skill['type'] ?? '')) === 'Ultimate' && !empty($skill['divinity_cost'])) {
                $hero->setDivinityCost((string) $skill['divinity_cost']);
                break;
            }
        }

        // Awakening bonuses — groupés par niveau (comme ascension par rang)
        $awakening = $data['awakening_bonuses'] ?? [];
        if ($awakening) {
            $byLevel = [];
            foreach ($awakening as $aw) {
                $level = $aw['level'] ?? '?';
                $type  = $aw['type'] ?? '';
                $desc  = $aw['description'] ?? '';
                if ($type === 'Stat' && !$desc) {
                    $stat = strtoupper($aw['stat'] ?? '');
                    $val  = $aw['flat_bonus'] ? "+{$aw['flat_bonus']}" : '+' . round(($aw['multiplier'] ?? 0) * 100) . '%';
                    $desc = "{$stat} {$val}";
                }
                $byLevel[$level][] = $desc;
            }
            $lines = [];
            foreach ($byLevel as $level => $descs) {
                $lines[] = "Level {$level}: " . implode(', ', array_filter($descs));
            }
            $hero->setAwakeningBonuses(implode("\n", $lines));
        }

        // Ascension bonuses (JSON brut pour le scaling JS)
        $ascension = $data['ascension_bonuses'] ?? [];
        $hero->setStatScalingJson($ascension ? json_encode($ascension) : null);
        if ($ascension) {
            $byRank = [];
            foreach ($ascension as $bonus) {
                $rank             = $bonus['rank'] ?? '?';
                $stat             = strtoupper($bonus['stat'] ?? '');
                $val              = $bonus['flat_bonus'] ? "+{$bonus['flat_bonus']}" : '+' . round(($bonus['multiplier'] ?? 0) * 100) . '%';
                $byRank[$rank][] = "{$stat} {$val}";
            }
            $lines = [];
            foreach ($byRank as $rank => $bonuses) {
                $lines[] = "Rank {$rank}: " . implode(', ', $bonuses);
            }
            $hero->setAscensionBonuses(implode("\n", $lines));
        }
    }

    private array $buffsCache    = [];
    private array $debuffsCache  = [];
    private array $disablesCache = [];
    private array $leadersCache  = [];

    private static array $LEADER_ICONS = [
        'HP'    => 'https://www.ravenpyros.com/application/files/8617/5451/0220/ICON_Leader_Health.png',
        'ATK'   => 'https://www.ravenpyros.com/application/files/1817/5451/0219/ICON_Leader_Attack.png',
        'DEF'   => 'https://www.ravenpyros.com/application/files/3117/5451/0219/ICON_Leader_Defence.png',
        'SPD'   => 'https://www.ravenpyros.com/application/files/3217/5451/0220/ICON_Leader_Speed.png',
        'FTH'   => 'https://www.ravenpyros.com/application/files/3717/5451/0220/ICON_Leader_Initiative.png',
        'ACC'   => 'https://www.ravenpyros.com/application/files/1917/5451/0220/ICON_Leader_Accuracy.png',
        'RES'   => 'https://www.ravenpyros.com/application/files/1217/5451/0220/ICON_Leader_Resistance.png',
        'CDMG'  => 'https://www.ravenpyros.com/application/files/8817/5451/0219/ICON_Leader_CriticalDamage.png',
        'CRATE' => 'https://www.ravenpyros.com/application/files/4817/5451/0219/ICON_Leader_CriticalRate.png',
    ];

    private static array $STAT_NORMALIZE = [
        'CRIT RATE' => 'CRATE',
        'CRIT DMG'  => 'CDMG',
        'C.DMG'     => 'CDMG',
        'INIT'      => 'FTH',
    ];

    private function syncLeaderBonus(Heroes $hero, array $skills): void
    {
        $leaderSkill = null;
        foreach ($skills as $skill) {
            if (($skill['type'] ?? '') === 'Leader Bonus') {
                $leaderSkill = $skill;
                break;
            }
        }

        if (!$leaderSkill) {
            $hero->setLeaderValue(null);
            $hero->setLeaderEntity(null);
            return;
        }

        $desc = trim($leaderSkill['description'] ?? '');
        $hero->setLeaderValue($desc ?: null);

        // Extraire le stat depuis "30% HP in ...", "40 ACC in ...", "20% CRIT RATE in ..."
        $stat = null;
        if (preg_match('/\d+%?\s+(.+?)\s+in\s+/i', $desc, $m)) {
            $raw  = strtoupper(trim($m[1]));
            $stat = self::$STAT_NORMALIZE[$raw] ?? $raw;
        }

        if (!$stat) {
            $hero->setLeaderEntity(null);
            return;
        }

        if (!isset($this->leadersCache[$stat])) {
            $entity = $this->em->getRepository(Leader::class)->findOneBy(['name' => $stat]);
            if (!$entity) {
                $entity = new Leader();
                $entity->setName($stat);
                $this->em->persist($entity);
            }
            $entity->setImageUrl(self::$LEADER_ICONS[$stat] ?? null);
            $this->leadersCache[$stat] = $entity;
        }

        $hero->setLeaderEntity($this->leadersCache[$stat]);
    }

    private function seedAllEffects(): void
    {
        $skip = ['Buff', 'Debuff', 'Disable'];
        foreach (SkillExtension::EFFECTS as $name => $data) {
            if (in_array($name, $skip, true)) {
                continue;
            }
            match ($data['type']) {
                'buff'    => $this->findOrCreateBuff($name),
                'debuff'  => $this->findOrCreateDebuff($name),
                'disable' => $this->findOrCreateDisable($name),
                default   => null,
            };
        }
        $this->em->flush();
    }

    private function syncEffects(Heroes $hero, array $skills): void
    {
        // Collecte les noms d'effets affichés (pas les "Hidden")
        $seen = [];
        foreach ($skills as $skill) {
            foreach ($skill['effects'] ?? [] as $effect) {
                if (($effect['visibility'] ?? '') !== 'Displayed') continue;
                $name = trim($effect['effect_name'] ?? '');
                if (!$name || isset($seen[$name])) continue;
                $seen[$name] = true;
            }
        }

        // Vide les liens existants
        foreach ($hero->getHeroBuffs()    as $b) $hero->getHeroBuffs()->removeElement($b);
        foreach ($hero->getHeroDebuffs()  as $d) $hero->getHeroDebuffs()->removeElement($d);
        foreach ($hero->getHeroDisables() as $d) $hero->getHeroDisables()->removeElement($d);

        foreach (array_keys($seen) as $name) {
            $type = SkillExtension::EFFECTS[$name]['type'] ?? null;
            if (!$type) continue;

            match ($type) {
                'buff'    => $hero->getHeroBuffs()->add($this->findOrCreateBuff($name)),
                'debuff'  => $hero->getHeroDebuffs()->add($this->findOrCreateDebuff($name)),
                'disable' => $hero->getHeroDisables()->add($this->findOrCreateDisable($name)),
                default   => null,
            };
        }
    }

    private function findOrCreateBuff(string $name): Buffs
    {
        if (!isset($this->buffsCache[$name])) {
            $entity = $this->em->getRepository(Buffs::class)->findOneBy(['name' => $name]);
            if (!$entity) {
                $entity = new Buffs();
                $entity->setName($name);
                $this->em->persist($entity);
            }
            $entity->setType('buff');
            $entity->setDescription(SkillExtension::EFFECTS[$name]['desc'] ?? null);
            $entity->setIconUrl(SkillExtension::iconUrl($name));
            $this->buffsCache[$name] = $entity;
        }
        return $this->buffsCache[$name];
    }

    private function findOrCreateDebuff(string $name): Debuffs
    {
        if (!isset($this->debuffsCache[$name])) {
            $entity = $this->em->getRepository(Debuffs::class)->findOneBy(['name' => $name]);
            if (!$entity) {
                $entity = new Debuffs();
                $entity->setName($name);
                $this->em->persist($entity);
            }
            $entity->setDescription(SkillExtension::EFFECTS[$name]['desc'] ?? null);
            $entity->setIconUrl(SkillExtension::iconUrl($name));
            $this->debuffsCache[$name] = $entity;
        }
        return $this->debuffsCache[$name];
    }

    private function findOrCreateDisable(string $name): Disable
    {
        if (!isset($this->disablesCache[$name])) {
            $entity = $this->em->getRepository(Disable::class)->findOneBy(['name' => $name]);
            if (!$entity) {
                $entity = new Disable();
                $entity->setName($name);
                $this->em->persist($entity);
            }
            $entity->setDescription(SkillExtension::EFFECTS[$name]['desc'] ?? null);
            $entity->setIconUrl(SkillExtension::iconUrl($name));
            $this->disablesCache[$name] = $entity;
        }
        return $this->disablesCache[$name];
    }

    private function apiGet(string $url): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_HTTPHEADER     => [
                'X-API-KEY: ' . $this->apiKey,
                'Accept: application/json',
                'User-Agent: OddOneGaming-Sync/1.0',
            ],
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return null;
        }
        if ($httpCode === 401) {
            throw new \RuntimeException('Clé API manquante (HTTP 401)');
        }
        if ($httpCode === 403) {
            throw new \RuntimeException('Clé API invalide (HTTP 403)');
        }
        if ($httpCode !== 200 || !$body) {
            return null;
        }

        $data = json_decode($body, true);
        return is_array($data) ? $data : null;
    }

    private function indexByName(array $entities): array
    {
        $map = [];
        foreach ($entities as $entity) {
            $map[$entity->getName()] = $entity;
        }
        return $map;
    }

    private function createEntity(string $class, string $name, array &$map): object
    {
        $entity = new $class();
        $entity->setName($name);
        $this->em->persist($entity);
        $map[$name] = $entity;
        return $entity;
    }
}
