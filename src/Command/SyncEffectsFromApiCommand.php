<?php

namespace App\Command;

use App\Entity\Buffs;
use App\Entity\Debuffs;
use App\Entity\Disable;
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
    name: 'app:sync-effects-from-api',
    description: 'Synchronise buffs/debuffs/disables depuis l\'API ravenpyros.com',
)]
class SyncEffectsFromApiCommand extends Command
{
    private const API_BASE = 'https://www.ravenpyros.com/api/public/v1';

    private const ENDPOINTS = [
        'buff'    => ['/buffs',    '/effects?type=buff'],
        'debuff'  => ['/debuffs',  '/effects?type=debuff'],
        'disable' => ['/disables', '/effects?type=disable'],
    ];

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
            ->addOption('force',    null, InputOption::VALUE_NONE, 'Écrase même les entrées modifiées manuellement')
            ->addOption('buffs',    null, InputOption::VALUE_NONE, 'Synchronise uniquement les buffs')
            ->addOption('debuffs',  null, InputOption::VALUE_NONE, 'Synchronise uniquement les debuffs')
            ->addOption('disables', null, InputOption::VALUE_NONE, 'Synchronise uniquement les disables');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $force  = $input->getOption('force');

        $anySpecific = $input->getOption('buffs') || $input->getOption('debuffs') || $input->getOption('disables');
        $doBuffs    = $input->getOption('buffs')    || !$anySpecific;
        $doDebuffs  = $input->getOption('debuffs')  || !$anySpecific;
        $doDisables = $input->getOption('disables') || !$anySpecific;

        $io->title('Synchronisation des effets — API ravenpyros.com');
        if ($force) {
            $io->note('Mode --force : les modifications manuelles seront écrasées.');
        }

        $totalErrors = 0;
        $anyEndpointFound = false;

        if ($doBuffs) {
            [$errors, $found] = $this->syncType($io, $dryRun, $force, 'buff', Buffs::class);
            $totalErrors += $errors;
            $anyEndpointFound = $anyEndpointFound || $found;
        }

        if ($doDebuffs) {
            [$errors, $found] = $this->syncType($io, $dryRun, $force, 'debuff', Debuffs::class);
            $totalErrors += $errors;
            $anyEndpointFound = $anyEndpointFound || $found;
        }

        if ($doDisables) {
            [$errors, $found] = $this->syncType($io, $dryRun, $force, 'disable', Disable::class);
            $totalErrors += $errors;
            $anyEndpointFound = $anyEndpointFound || $found;
        }

        if (!$anyEndpointFound) {
            $io->warning([
                'Aucun endpoint d\'effets trouvé sur l\'API ravenpyros.',
                'L\'API ne semble pas encore exposer les buffs/debuffs/disables.',
                'Utilise plutôt : php bin/console app:init-effects-from-code',
            ]);
            return Command::FAILURE;
        }

        if (!$dryRun) {
            $this->em->flush();
        }

        if ($totalErrors > 0) {
            $io->warning(sprintf('%d erreur(s) au total.', $totalErrors));
            return Command::FAILURE;
        }

        $io->success('Effets synchronisés avec succès.');
        return Command::SUCCESS;
    }

    /**
     * @return array{int, bool} [errorCount, endpointFound]
     */
    private function syncType(SymfonyStyle $io, bool $dryRun, bool $force, string $type, string $entityClass): array
    {
        $io->section(ucfirst($type) . 's');

        $response = null;
        foreach (self::ENDPOINTS[$type] as $path) {
            $response = $this->apiGet(self::API_BASE . $path);
            if ($response !== null) {
                $io->writeln(sprintf('<info>Endpoint trouvé : %s</info>', $path));
                break;
            }
        }

        if ($response === null) {
            $io->writeln(sprintf('<comment>Aucun endpoint disponible pour les %ss — ignoré.</comment>', $type));
            return [0, false];
        }

        $list      = $response['data'] ?? [];
        $created   = 0;
        $updated   = 0;
        $skipped   = 0;
        $errors    = [];

        $io->writeln(sprintf('<info>%d %ss reçus</info>', count($list), $type));

        foreach ($list as $data) {
            $name = $data['name'] ?? null;
            if (!$name) {
                continue;
            }

            $apiDesc    = $data['description'] ?? $data['desc'] ?? null;
            $apiIconUrl = $data['icon_url'] ?? $data['iconUrl'] ?? null;

            if ($dryRun) {
                $io->writeln(sprintf('  [DRY-RUN] %s', $name));
                continue;
            }

            try {
                $entity = $this->em->getRepository($entityClass)->findOneBy(['name' => $name]);
                $isNew  = $entity === null;

                if ($isNew) {
                    $entity = new $entityClass();
                    $entity->setName($name);
                    $entity->setType($type);
                    $entity->setDescription($apiDesc);
                    $entity->setIconUrl($apiIconUrl);
                    $this->em->persist($entity);
                    $created++;
                    $io->writeln(sprintf('  <comment>+</comment> %s <comment>[nouveau]</comment>', $name));
                    continue;
                }

                // Valeurs originales depuis la constante (référence avant toute édition manuelle)
                $originalDesc    = SkillExtension::EFFECTS[$name]['desc'] ?? null;
                $originalIconUrl = SkillExtension::iconUrl($name);

                $descProtected    = $entity->getDescription() !== null && $entity->getDescription() !== $originalDesc;
                $iconUrlProtected = $entity->getIconUrl() !== null && $entity->getIconUrl() !== $originalIconUrl;

                if (!$force && ($descProtected || $iconUrlProtected)) {
                    $io->writeln(sprintf(
                        '  <comment>⚠ %s — modifié manuellement, ignoré (utilise --force pour écraser)</comment>',
                        $name
                    ));
                    $skipped++;
                    continue;
                }

                $entity->setDescription($apiDesc);
                $entity->setIconUrl($apiIconUrl);
                $updated++;
                $io->writeln(sprintf('  <info>✓</info> %s', $name));

            } catch (\Throwable $e) {
                $errors[] = sprintf('%s : %s', $name, $e->getMessage());
                $io->writeln(sprintf('  <error>✗ %s — %s</error>', $name, $e->getMessage()));
            }
        }

        if (!$dryRun) {
            $io->writeln(sprintf(
                '<info>%d créés, %d mis à jour, %d protégés (édition manuelle), %d erreurs</info>',
                $created, $updated, $skipped, count($errors)
            ));
        } else {
            $io->note(sprintf('Dry-run : %d %ss seraient traités.', count($list), $type));
        }

        return [count($errors), true];
    }

    private function apiGet(string $url): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 15,
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
