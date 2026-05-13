<?php

namespace App\Command;

use App\Entity\Affinity;
use App\Entity\Allegiance;
use App\Entity\Faction;
use App\Entity\Heroes;
use App\Entity\Rarity;
use App\Entity\Type;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:import-heroes-json',
    description: 'Importe les héros depuis var/godforge-heroes-db.json vers la base de données',
)]
class ImportHeroesFromJsonCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private SlugService $slugService,
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simule sans modifier la DB');
        $this->addOption('reset',   null, InputOption::VALUE_NONE, 'Supprime tous les héros existants avant import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $reset  = $input->getOption('reset');

        $jsonPath = $this->projectDir . '/var/godforge-heroes-db.json';
        if (!file_exists($jsonPath)) {
            $io->error('Fichier introuvable : var/godforge-heroes-db.json');
            return Command::FAILURE;
        }

        $raw = json_decode(file_get_contents($jsonPath), true);
        if (!$raw) {
            $io->error('JSON invalide ou vide.');
            return Command::FAILURE;
        }

        // Supporte l'ancien format (objet plat) ET le nouveau format {version, data: [...]}
        $heroes = isset($raw['data']) ? array_values($raw['data']) : array_values($raw);

        $io->title(sprintf('Import de %d héros depuis godforge-heroes-db.json', count($heroes)));

        if ($reset && !$dryRun) {
            $deleted = $this->em->createQuery('DELETE FROM App\Entity\Heroes h')->execute();
            $io->warning(sprintf('%d héros supprimés.', $deleted));
        }

        $factions    = $this->indexByName($this->em->getRepository(Faction::class)->findAll());
        $rarities    = $this->indexByName($this->em->getRepository(Rarity::class)->findAll());
        $affinities  = $this->indexByName($this->em->getRepository(Affinity::class)->findAll());
        $allegiances = $this->indexByName($this->em->getRepository(Allegiance::class)->findAll());
        $types       = $this->indexByName($this->em->getRepository(Type::class)->findAll());

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($heroes as $heroData) {
            $name = $heroData['name'] ?? null;
            if (!$name) {
                continue;
            }

            if (!$reset) {
                $existing = $this->em->getRepository(Heroes::class)->findOneBy(['Name' => $name]);
                if ($existing) {
                    $io->writeln(sprintf('  <comment>⏭  %s — déjà en DB</comment>', $name));
                    $skipped++;
                    continue;
                }
            }

            if ($dryRun) {
                $io->writeln(sprintf('  <info>[DRY-RUN]</info> %s (%s / %s)', $name, $heroData['faction'] ?? '?', $heroData['rarity'] ?? '?'));
                $imported++;
                continue;
            }

            try {
                $hero = new Heroes();
                $hero->setName($name);
                $hero->setSlug($this->slugService->uniqueHeroSlug($name, null));

                // Relations
                if ($heroData['faction'] ?? null) {
                    $hero->setFactionEntity($factions[$heroData['faction']] ?? $this->createFaction($heroData['faction'], $factions));
                }
                if ($heroData['rarity'] ?? null) {
                    $hero->setRarityEntity($rarities[$heroData['rarity']] ?? null);
                }
                if ($heroData['affinity'] ?? null) {
                    $hero->setAffinityEntity($affinities[$heroData['affinity']] ?? null);
                }
                if ($heroData['allegiance'] ?? null) {
                    $hero->setAllegianceEntity($allegiances[$heroData['allegiance']] ?? null);
                }
                if ($heroData['archetype'] ?? null) {
                    $hero->setTypeEntity($types[$heroData['archetype']] ?? null);
                }

                // Skills — nouveau format indexé numériquement avec type en minuscule
                $skills = $heroData['skills'] ?? $heroData['abilities'] ?? [];
                foreach ($skills as $skill) {
                    $type    = ucfirst(strtolower($skill['type'] ?? ''));
                    $packed  = ($skill['name'] ?? '') . '|||' . ($skill['description'] ?? '');
                    match ($type) {
                        'Basic'   => $hero->setBase($packed),
                        'Core'    => $hero->setCore($packed),
                        'Ultimate'=> $hero->setUltimate($packed),
                        'Passive' => !$hero->getPassive() ? $hero->setPassive($packed) : null,
                        default   => null,
                    };
                }

                // Awakening bonuses
                $awakening = $heroData['awakening_bonuses'] ?? $heroData['awakening'] ?? [];
                if (!empty($awakening)) {
                    $lines = [];
                    foreach ($awakening as $aw) {
                        if (is_string($aw)) {
                            $lines[] = $aw;
                        } elseif (!empty($aw['description'] ?? $aw['upgrade_description'] ?? '')) {
                            $desc  = $aw['description'] ?: ($aw['upgrade_description'] ?? '');
                            $level = $aw['level'] ?? '';
                            $type  = $aw['type'] ?? '';
                            $lines[] = "Level {$level}" . ($type ? " ({$type})" : '') . ($desc ? ": {$desc}" : '');
                        }
                    }
                    if ($lines) {
                        $hero->setAwakeningBonuses(implode("\n", $lines));
                    }
                }

                // Ascension bonuses
                $ascension = $heroData['ascension_bonuses'] ?? $heroData['ascension'] ?? [];
                if (!empty($ascension)) {
                    if (is_array($ascension) && isset($ascension[0]) && is_array($ascension[0])) {
                        // Nouveau format structuré — groupe par rank
                        $byRank = [];
                        foreach ($ascension as $bonus) {
                            $rank = $bonus['rank'] ?? '?';
                            $stat = strtoupper($bonus['stat'] ?? '');
                            $val  = $bonus['flat_bonus'] ? "+{$bonus['flat_bonus']}" : '+' . round(($bonus['multiplier'] ?? 0) * 100) . '%';
                            $byRank[$rank][] = "{$stat} {$val}";
                        }
                        $lines = [];
                        foreach ($byRank as $rank => $bonuses) {
                            $lines[] = "Rank {$rank}: " . implode(', ', $bonuses);
                        }
                        $hero->setAscensionBonuses(implode("\n", $lines));
                    } else {
                        $hero->setAscensionBonuses(implode("\n", $ascension));
                    }
                }

                // Leader bonus
                $leaderDesc = $heroData['leaderBonus']['description'] ?? null;
                if ($leaderDesc) {
                    $hero->setLeaderValue(strip_tags($leaderDesc));
                }

                // Divinity
                $divinity = $heroData['divinity'] ?? null;
                if ($divinity) {
                    $hero->setDivinityCost((string)($divinity['required'] ?? ''));
                    $hero->setInitialDivinity((string)($divinity['initial'] ?? ''));
                } else {
                    // Cherche dans les skills (nouveau format : divinity_cost par skill)
                    foreach ($skills as $skill) {
                        if (ucfirst(strtolower($skill['type'] ?? '')) === 'Ultimate' && !empty($skill['divinity_cost'])) {
                            $hero->setDivinityCost((string)$skill['divinity_cost']);
                            break;
                        }
                    }
                }

                $this->em->persist($hero);
                $imported++;

                $io->writeln(sprintf('  <info>✓</info>  %s', $name));

            } catch (\Throwable $e) {
                $errors[] = sprintf('%s : %s', $name, $e->getMessage());
                $io->writeln(sprintf('  <error>✗  %s</error>', $name));
            }
        }

        if (!$dryRun) {
            $this->em->flush();
        }

        $io->newLine();
        $io->success(sprintf('%d importés, %d ignorés, %d erreurs', $imported, $skipped, count($errors)));

        if (!empty($errors)) {
            $io->section('Erreurs');
            $io->listing($errors);
        }

        $io->note('Lance maintenant : php bin/console app:scrape-hero-images');

        return Command::SUCCESS;
    }

    private function indexByName(array $entities): array
    {
        $map = [];
        foreach ($entities as $entity) {
            $map[$entity->getName()] = $entity;
        }
        return $map;
    }

    private function createFaction(string $name, array &$factions): Faction
    {
        $faction = new Faction();
        $faction->setName($name);
        $this->em->persist($faction);
        $factions[$name] = $faction;
        return $faction;
    }
}
