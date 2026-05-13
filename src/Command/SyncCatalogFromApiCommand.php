<?php

namespace App\Command;

use App\Entity\Imprints;
use App\Entity\Rarity;
use App\Entity\Sets;
use App\Entity\Weapons;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:sync-catalog-api',
    description: 'Synchronise les weapons, imprints et armor sets depuis l\'API ravenpyros.com',
)]
class SyncCatalogFromApiCommand extends Command
{
    private const API_BASE = 'https://www.ravenpyros.com/api/public/v1';

    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%env(RAVENPYROS_API_KEY)%')] private string $apiKey,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run',  null, InputOption::VALUE_NONE, 'Simule sans modifier la DB')
            ->addOption('weapons',  null, InputOption::VALUE_NONE, 'Synchronise uniquement les weapons')
            ->addOption('imprints', null, InputOption::VALUE_NONE, 'Synchronise uniquement les imprints')
            ->addOption('sets',     null, InputOption::VALUE_NONE, 'Synchronise uniquement les armor sets');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');

        // Sans option spécifique, on sync tout
        $anySpecific = $input->getOption('weapons') || $input->getOption('imprints') || $input->getOption('sets');
        $doWeapons  = $input->getOption('weapons')  || !$anySpecific;
        $doImprints = $input->getOption('imprints') || !$anySpecific;
        $doSets     = $input->getOption('sets')     || !$anySpecific;

        $io->title('Synchronisation du catalogue — API ravenpyros.com');

        $totalErrors = 0;

        if ($doWeapons) {
            $totalErrors += $this->syncWeapons($io, $dryRun);
        }

        if ($doImprints) {
            $totalErrors += $this->syncImprints($io, $dryRun);
        }

        if ($doSets) {
            $totalErrors += $this->syncSets($io, $dryRun);
        }

        if (!$dryRun) {
            $this->em->flush();
        }

        if ($totalErrors > 0) {
            $io->warning(sprintf('%d erreur(s) au total.', $totalErrors));
            return Command::FAILURE;
        }

        $io->success('Catalogue synchronisé avec succès.');
        return Command::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // WEAPONS
    // -------------------------------------------------------------------------

    private function syncWeapons(SymfonyStyle $io, bool $dryRun): int
    {
        $io->section('Weapons');

        $response = $this->apiGet(self::API_BASE . '/weapons');
        if ($response === null) {
            $io->error('Impossible de récupérer les weapons depuis l\'API.');
            return 1;
        }

        $list    = $response['data'] ?? [];
        $created = 0;
        $updated = 0;
        $errors  = [];

        $io->writeln(sprintf('<info>%d weapons reçus (version : %s)</info>', count($list), $response['version'] ?? '?'));

        foreach ($list as $data) {
            $name = $data['name'] ?? null;
            if (!$name) {
                continue;
            }

            if ($dryRun) {
                $io->writeln(sprintf('  [DRY-RUN] %s (%s / %s)', $name, $data['rarity'] ?? '?', $data['faction'] ?? '?'));
                continue;
            }

            try {
                $weapon = $this->em->getRepository(Weapons::class)->findOneBy(['name' => $name]);
                $isNew  = $weapon === null;

                if ($isNew) {
                    $weapon = new Weapons();
                    $weapon->setName($name);
                }

                // Champs de base — les liaisons heroes ne sont jamais touchées
                $weapon->setRarity($data['rarity'] ?? null);
                $weapon->setMainStat($data['primary_stat'] ?? null);
                $weapon->setDescription($data['description'] ?? null);
                $weapon->setFaction($data['faction'] ?? null);
                $weapon->setImageUrl($data['icon_url'] ?? null);

                if ($isNew) {
                    $this->em->persist($weapon);
                    $created++;
                    $io->writeln(sprintf('  <comment>+</comment> %s <comment>[nouveau]</comment>', $name));
                } else {
                    $updated++;
                    $io->writeln(sprintf('  <info>✓</info> %s', $name));
                }

            } catch (\Throwable $e) {
                $errors[] = sprintf('%s : %s', $name, $e->getMessage());
                $io->writeln(sprintf('  <error>✗ %s — %s</error>', $name, $e->getMessage()));
            }
        }

        if (!$dryRun) {
            $io->writeln(sprintf('<info>%d créés, %d mis à jour, %d erreurs</info>', $created, $updated, count($errors)));
        } else {
            $io->note(sprintf('Dry-run : %d weapons seraient synchronisés.', count($list)));
        }

        return count($errors);
    }

    // -------------------------------------------------------------------------
    // IMPRINTS
    // -------------------------------------------------------------------------

    private function syncImprints(SymfonyStyle $io, bool $dryRun): int
    {
        $io->section('Imprints');

        $response = $this->apiGet(self::API_BASE . '/imprints');
        if ($response === null) {
            $io->error('Impossible de récupérer les imprints depuis l\'API.');
            return 1;
        }

        $list = $response['data'] ?? [];
        $io->writeln(sprintf('<info>%d imprints reçus</info>', count($list)));

        // Pré-charge les rarités pour le mapping
        $rarities = [];
        if (!$dryRun) {
            foreach ($this->em->getRepository(Rarity::class)->findAll() as $r) {
                $rarities[$r->getName()] = $r;
            }
        }

        $created = 0;
        $updated = 0;
        $errors  = [];

        foreach ($list as $data) {
            $name = $data['name'] ?? null;
            if (!$name) {
                continue;
            }

            if ($dryRun) {
                $io->writeln(sprintf('  [DRY-RUN] %s (%s — héros : %s)', $name, $data['hero_rarity'] ?? '?', $data['hero_name'] ?? '?'));
                continue;
            }

            try {
                $imprint = $this->em->getRepository(Imprints::class)->findOneBy(['name' => $name]);
                $isNew   = $imprint === null;

                if ($isNew) {
                    $imprint = new Imprints();
                    $imprint->setName($name);
                }

                // Champs de base — les liaisons heroes ne sont jamais touchées
                $imprint->setDescription($data['description'] ?? null);
                $imprint->setImageUrl($data['hero_portrait_url'] ?? null);

                // Rarity via relation
                $rarityName = $data['hero_rarity'] ?? null;
                if ($rarityName && isset($rarities[$rarityName])) {
                    $imprint->setRarity($rarities[$rarityName]);
                }

                if ($isNew) {
                    $this->em->persist($imprint);
                    $created++;
                    $io->writeln(sprintf('  <comment>+</comment> %s <comment>[nouveau]</comment>', $name));
                } else {
                    $updated++;
                    $io->writeln(sprintf('  <info>✓</info> %s', $name));
                }

            } catch (\Throwable $e) {
                $errors[] = sprintf('%s : %s', $name, $e->getMessage());
                $io->writeln(sprintf('  <error>✗ %s — %s</error>', $name, $e->getMessage()));
            }
        }

        if (!$dryRun) {
            $io->writeln(sprintf('<info>%d créés, %d mis à jour, %d erreurs</info>', $created, $updated, count($errors)));
        } else {
            $io->note(sprintf('Dry-run : %d imprints seraient synchronisés.', count($list)));
        }

        return count($errors);
    }

    // -------------------------------------------------------------------------
    // ARMOR SETS
    // -------------------------------------------------------------------------

    private function syncSets(SymfonyStyle $io, bool $dryRun): int
    {
        $io->section('Armor Sets');

        $response = $this->apiGet(self::API_BASE . '/armor-sets');
        if ($response === null) {
            $io->warning('Endpoint /armor-sets non disponible — aucun set synchronisé.');
            return 0;
        }

        $list    = $response['data'] ?? [];
        $created = 0;
        $updated = 0;
        $errors  = [];

        $io->writeln(sprintf('<info>%d armor sets reçus (version : %s)</info>', count($list), $response['version'] ?? '?'));

        foreach ($list as $data) {
            $name = $data['name'] ?? null;
            if (!$name) {
                continue;
            }

            if ($dryRun) {
                $io->writeln(sprintf('  [DRY-RUN] %s (%d-piece)', $name, $data['piece_type'] ?? $data['pieces'] ?? 0));
                continue;
            }

            try {
                $set  = $this->em->getRepository(Sets::class)->findOneBy(['name' => $name]);
                $isNew = $set === null;

                if ($isNew) {
                    $set = new Sets();
                    $set->setName($name);
                }

                $set->setImageUrl($data['icon_url'] ?? $data['image_url'] ?? null);
                $set->setEffect($data['description'] ?? $data['effect'] ?? null);
                $set->setBaseName($data['base_name'] ?? $data['set_name'] ?? preg_replace('/\s*\(\d+-piece\)$/i', '', $name));
                $pieceType = (int) ($data['piece_type'] ?? $data['pieces'] ?? 0);
                $set->setPieceType($pieceType ?: null);

                if ($isNew) {
                    $this->em->persist($set);
                    $created++;
                    $io->writeln(sprintf('  <comment>+</comment> %s <comment>[nouveau]</comment>', $name));
                } else {
                    $updated++;
                    $io->writeln(sprintf('  <info>✓</info> %s', $name));
                }

            } catch (\Throwable $e) {
                $errors[] = sprintf('%s : %s', $name, $e->getMessage());
                $io->writeln(sprintf('  <error>✗ %s — %s</error>', $name, $e->getMessage()));
            }
        }

        if (!$dryRun) {
            $io->writeln(sprintf('<info>%d créés, %d mis à jour, %d erreurs</info>', $created, $updated, count($errors)));
        } else {
            $io->note(sprintf('Dry-run : %d armor sets seraient synchronisés.', count($list)));
        }

        return count($errors);
    }

    // -------------------------------------------------------------------------
    // HTTP
    // -------------------------------------------------------------------------

    private function apiGet(string $url): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'X-API-KEY: ' . $this->apiKey,
                'Accept: application/json',
                'User-Agent: OddOneGaming-Sync/1.0',
            ],
        ]);

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

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
}
