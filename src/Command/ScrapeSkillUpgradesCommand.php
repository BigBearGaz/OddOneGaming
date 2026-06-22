<?php

namespace App\Command;

use App\Entity\Heroes;
use App\Entity\SkillUpgrade;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:scrape-skill-upgrades',
    description: 'Scrape les Level Upgrades depuis ravenpyros.com/heroes/{slug} pour tous les héros',
)]
class ScrapeSkillUpgradesCommand extends Command
{
    use CommandGuardTrait;

    private const BASE_URL   = 'https://www.ravenpyros.com/heroes/';
    private const TYPE_MAP   = ['Basic' => 'base', 'Core' => 'core', 'Ultimate' => 'ultimate', 'Passive' => 'passive'];
    private const SLEEP_MS   = 400; // ms entre chaque requête

    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%env(RAVENPYROS_API_KEY)%')] private string $apiKey,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('slug',    null, InputOption::VALUE_REQUIRED, 'Slug héros en base (ex: yan-wang)')
            ->addOption('rp-slug', null, InputOption::VALUE_REQUIRED, 'Slug sur ravenpyros si différent (ex: yan-wangs)')
            ->addOption('force',   null, InputOption::VALUE_NONE,     'Réécrit même si les données existent déjà');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $force  = $input->getOption('force');
        $slug   = $input->getOption('slug');
        $rpSlug = $input->getOption('rp-slug');

        if (!$this->acquireLock($this->getName())) {
            $io->error('La commande est déjà en cours d\'exécution. Abandon pour éviter les conflits.');
            return Command::FAILURE;
        }
        $this->applyResourceLimits();

        $io->title('Scrape Level Upgrades — ravenpyros.com');

        if ($slug) {
            $heroes = $this->em->getRepository(Heroes::class)->findBy(['slug' => $slug]);
            if (empty($heroes)) {
                $io->error("Héros introuvable en base : $slug");
                return Command::FAILURE;
            }
        } else {
            $heroes = $this->em->getRepository(Heroes::class)->findAll();
        }

        $io->note(sprintf('%d héros à traiter', count($heroes)));

        $ok = 0;
        $skip = 0;
        $errors = [];

        foreach ($heroes as $hero) {
            // Skip si déjà rempli et pas --force
            if (!$force && $this->hasExistingData($hero)) {
                $io->writeln(sprintf('  <comment>~</comment> %s <comment>[déjà rempli, skip]</comment>', $hero->getName()));
                $skip++;
                continue;
            }

            $url  = self::BASE_URL . ($rpSlug && count($heroes) === 1 ? $rpSlug : $hero->getSlug());
            $html = $this->fetchPage($url);

            if ($html === null) {
                $io->writeln(sprintf('  <error>✗ %s — page introuvable (%s)</error>', $hero->getName(), $url));
                $errors[] = $hero->getName();
                continue;
            }

            $upgrades = $this->parseSkillUpgrades($html);

            if (empty($upgrades)) {
                $io->writeln(sprintf('  <comment>-</comment> %s <comment>[aucun level upgrade trouvé]</comment>', $hero->getName()));
                $skip++;
                continue;
            }

            $this->saveUpgrades($hero, $upgrades, $force);
            $ok++;
            $io->writeln(sprintf('  <info>✓</info> %s — %d skills mis à jour', $hero->getName(), count($upgrades)));

            usleep(self::SLEEP_MS * 1000);
        }

        $this->em->flush();

        $io->newLine();
        $io->success(sprintf('%d héros mis à jour, %d skippés, %d erreurs', $ok, $skip, count($errors)));

        if ($errors) {
            $io->listing($errors);
        }

        return Command::SUCCESS;
    }

    private function hasExistingData(Heroes $hero): bool
    {
        foreach ($hero->getSkillUpgrades() as $su) {
            if ($su->getLevel1() || $su->getLevel2() || $su->getLevel3()) {
                return true;
            }
        }
        return false;
    }

    private function fetchPage(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => [
                'Accept: text/html',
                'User-Agent: OddOneGaming-Bot/1.0',
            ],
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$body) {
            return null;
        }

        return $body;
    }

    /**
     * Retourne ['base' => ['Damage +5%', ...], 'core' => [...], ...]
     */
    private function parseSkillUpgrades(string $html): array
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();

        $xpath   = new \DOMXPath($doc);
        $result  = [];

        // Chaque skill est dans un div.skill-entry
        $skillEntries = $xpath->query('//div[contains(@class,"skill-entry")]');

        foreach ($skillEntries as $entry) {
            // Détermine le type du skill (Basic/Core/Ultimate/Passive)
            $typeNodes = $xpath->query('.//small[contains(@class,"text-body-secondary")]', $entry);
            $rawType   = trim($typeNodes->item(0)?->textContent ?? '');
            $skillType = self::TYPE_MAP[$rawType] ?? null;

            if (!$skillType) {
                continue;
            }

            // Cherche le h5 "Level Upgrades"
            $h5Nodes = $xpath->query('.//h5', $entry);
            $luH5    = null;
            foreach ($h5Nodes as $h5) {
                if (stripos(trim($h5->textContent), 'Level Upgrades') !== false) {
                    $luH5 = $h5;
                    break;
                }
            }

            if (!$luH5) {
                continue;
            }

            // La liste des niveaux est le ul suivant le h5
            $ul = null;
            $node = $luH5->nextSibling;
            while ($node) {
                if ($node instanceof \DOMElement && strtolower($node->tagName) === 'ul') {
                    $ul = $node;
                    break;
                }
                $node = $node->nextSibling;
            }

            if (!$ul) {
                continue;
            }

            $levels = [];
            foreach ($ul->childNodes as $li) {
                if (!($li instanceof \DOMElement) || strtolower($li->tagName) !== 'li') {
                    continue;
                }
                // Supprime le "Level X:" (contenu du strong) et garde le reste
                $strong = $xpath->query('.//strong', $li)->item(0);
                if ($strong) {
                    $strong->parentNode->removeChild($strong);
                }
                $text = trim($li->textContent);
                // Nettoie le ":" résiduel en début
                $text = ltrim($text, ': ');
                if ($text !== '') {
                    $levels[] = $text;
                }
            }

            if (!empty($levels)) {
                $result[$skillType] = $levels;
            }
        }

        return $result;
    }

    private function saveUpgrades(Heroes $hero, array $upgrades, bool $force): void
    {
        foreach ($upgrades as $type => $levels) {
            $su = $hero->getSkillUpgradeByType($type);

            if (!$su) {
                $su = new SkillUpgrade();
                $su->setHero($hero);
                $su->setSkillType($type);
                $this->em->persist($su);
            }

            $su->setLevel1($levels[0] ?? null);
            $su->setLevel2($levels[1] ?? null);
            $su->setLevel3($levels[2] ?? null);
            $su->setLevel4($levels[3] ?? null);
            $su->setLevel5($levels[4] ?? null);
            $su->setLevel6($levels[5] ?? null);
        }
    }
}
