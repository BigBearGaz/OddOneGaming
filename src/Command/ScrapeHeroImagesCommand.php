<?php

namespace App\Command;

use App\Entity\Heroes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:scrape-hero-images',
    description: 'Télécharge portraits et icônes de skills depuis l\'API ravenpyros.com',
)]
class ScrapeHeroImagesCommand extends Command
{
    private const API_BASE = 'https://www.ravenpyros.com/api/public/v1';

    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
        #[Autowire('%env(RAVENPYROS_API_KEY)%')] private string $apiKey,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simule sans télécharger ni modifier la DB')
            ->addOption('force',   null, InputOption::VALUE_NONE, 'Réécrase les images déjà téléchargées');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io     = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $force  = $input->getOption('force');

        // Dossiers locaux
        $portraitsDir  = $this->projectDir . '/public/images/portraits';
        $abilitiesDir  = $this->projectDir . '/public/images/abilities';

        if (!$dryRun) {
            foreach ([$portraitsDir, $abilitiesDir] as $dir) {
                if (!is_dir($dir)) mkdir($dir, 0755, true);
            }
        }

        // Récupère tous les héros avec détails depuis l'API (1 seul appel)
        $io->writeln('Récupération des données depuis l\'API…');
        $response = $this->apiGet(self::API_BASE . '/heroes?details=true');
        if (!$response) {
            $io->error('Impossible de récupérer les données depuis l\'API.');
            return Command::FAILURE;
        }

        $apiHeroes = [];
        foreach ($response['data'] ?? [] as $h) {
            $apiHeroes[$h['name']] = $h;
        }

        $heroes = $this->em->getRepository(Heroes::class)->findAll();
        $io->title(sprintf('Téléchargement des images pour %d héros (portrait + skills)', count($heroes)));

        $success = 0;
        $skipped = 0;
        $failed  = [];

        foreach ($heroes as $hero) {
            $slug      = $hero->getSlug();
            $apiData   = $apiHeroes[$hero->getName()] ?? null;

            if (!$apiData) {
                $io->writeln(sprintf('  <comment>⏭  %s — introuvable dans l\'API</comment>', $hero->getName()));
                continue;
            }

            $io->writeln(sprintf('<comment>── %s</comment>', $hero->getName()));

            // Portrait
            $portraitUrl = $apiData['portrait_url'] ?? null;
            if ($portraitUrl) {
                $ext    = pathinfo(parse_url($portraitUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'webp';
                $result = $this->processImage(
                    url:       $portraitUrl,
                    localPath: $portraitsDir . '/' . $slug . '.' . $ext,
                    label:     'portrait',
                    dryRun:    $dryRun,
                    force:     $force,
                    output:    $output,
                );
                if ($result === 'ok' || $result === 'skipped') {
                    if ($result === 'ok' && !$dryRun) {
                        $hero->setImageUrl('/images/portraits/' . $slug . '.' . $ext);
                    }
                    $result === 'ok' ? $success++ : $skipped++;
                } else {
                    $failed[] = $hero->getName() . ' (portrait)';
                }
            }

            // Icônes de skills
            $skills = $apiData['skills'] ?? [];
            $seen   = [];
            foreach ($skills as $skill) {
                $type = strtolower($skill['type'] ?? '');
                if (!in_array($type, ['basic', 'core', 'passive', 'ultimate']) || isset($seen[$type])) {
                    continue;
                }
                $seen[$type] = true;

                $iconUrl = $skill['icon_url'] ?? null;
                if (!$iconUrl) continue;

                $iconExt = pathinfo(parse_url($iconUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'webp';
                $result  = $this->processImage(
                    url:       $iconUrl,
                    localPath: $abilitiesDir . '/' . $slug . '_' . $type . '.' . $iconExt,
                    label:     $type,
                    dryRun:    $dryRun,
                    force:     $force,
                    output:    $output,
                );

                if ($result === 'ok') $success++;
                elseif ($result === 'skipped') $skipped++;
                else $failed[] = $hero->getName() . ' (' . $type . ')';
            }
        }

        if (!$dryRun) {
            $this->em->flush();
        }

        $io->newLine();
        $io->success(sprintf('%d téléchargés, %d ignorés, %d échecs', $success, $skipped, count($failed)));

        if (!empty($failed)) {
            $io->section('Échecs');
            $io->listing($failed);
        }

        return Command::SUCCESS;
    }

    private function processImage(string $url, string $localPath, string $label, bool $dryRun, bool $force, OutputInterface $output): string
    {
        if (!$force && file_exists($localPath)) {
            $output->writeln(sprintf('    ⏭  %s — déjà présent', $label));
            return 'skipped';
        }

        if ($dryRun) {
            $output->writeln(sprintf('    [DRY-RUN] %s → %s', $label, $url));
            return 'ok';
        }

        $data = $this->downloadFile($url);
        if ($data === false) {
            $output->writeln(sprintf('    ✗  %s — échec (%s)', $label, $url));
            return 'failed';
        }

        file_put_contents($localPath, $data);
        $output->writeln(sprintf('    ✓  %s', $label));
        return 'ok';
    }

    private function downloadFile(string $url): string|false
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'User-Agent: OddOneGaming-Sync/1.0',
                'Referer: https://www.ravenpyros.com/',
                'Accept: image/webp,image/*,*/*;q=0.8',
            ],
        ]);

        $data     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode === 200 && $data !== false) ? $data : false;
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
        curl_close($ch);

        if ($httpCode !== 200 || !$body) return null;
        $data = json_decode($body, true);
        return is_array($data) ? $data : null;
    }
}
