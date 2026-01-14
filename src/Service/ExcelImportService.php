<?php

namespace App\Service;

use App\Entity\Heroes;
use App\Repository\HeroesRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportService
{
    private EntityManagerInterface $entityManager;
    private HeroesRepository $heroesRepository;

    public function __construct(EntityManagerInterface $entityManager, HeroesRepository $heroesRepository)
    {
        $this->entityManager = $entityManager;
        $this->heroesRepository = $heroesRepository;
    }

    public function importHeroes(string $filePath, bool $clearBefore = true): array
    {
        $errors = [];
        $imported = 0;
        $deleted = 0;

        try {
            // Supprime tous les héros si demandé
            if ($clearBefore) {
                $existingHeroes = $this->heroesRepository->findAll();
                foreach ($existingHeroes as $hero) {
                    $this->entityManager->remove($hero);
                    $deleted++;
                }
                $this->entityManager->flush();
            }

            // Charge le fichier Excel
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Ignore la première ligne (en-têtes)
            foreach (array_slice($rows, 1) as $index => $row) {
                // Saute les lignes vides
                if (empty($row[1])) {
                    continue;
                }

                try {
                    $hero = new Heroes();

                    // Colonnes 0-16 : données de base
                    $hero->setName($row[1] ?? '');
                    $hero->setFaction($row[2] ?? null);
                    $hero->setType($row[3] ?? null);
                    $hero->setAffinity($row[4] ?? null);
                    $hero->setAllegiance($row[5] ?? null);
                    $hero->setWeapons1($row[6] ?? null);
                    $hero->setWeapons2($row[7] ?? null);
                    $hero->setLeader($row[8] ?? null);
                    $hero->setBase($row[9] ?? null);
                    $hero->setCore($row[10] ?? null);
                    $hero->setUltimate($row[11] ?? null);
                    $hero->setPassive($row[12] ?? null);
                    $hero->setImprint($row[13] ?? null);
                    $hero->setImprint1($row[14] ?? null);
                    $hero->setImprint2($row[15] ?? null);
                    $hero->setImprint3($row[16] ?? null);
                    
                    // Colonnes 17-19 : Buffs, Debuffs, Disables ⚠️ AJOUTÉ
                    $hero->setBuffs($row[17] ?? null);
                    $hero->setDebuffs($row[18] ?? null);
                    $hero->setDisable($row[19] ?? null);
                    
                    // Colonnes 20-21 : Images et Vidéos
                    $hero->setImageUrl($row[20] ?? null);
                    $hero->setVideosUrl($row[21] ?? null);

                    $this->entityManager->persist($hero);
                    $imported++;

                    // Flush tous les 20 héros
                    if ($imported % 20 === 0) {
                        $this->entityManager->flush();
                    }

                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            // Flush final
            $this->entityManager->flush();

            // Reset auto-increment
            if ($clearBefore && $deleted > 0) {
                try {
                    $connection = $this->entityManager->getConnection();
                    $connection->executeStatement('ALTER TABLE heroes AUTO_INCREMENT = 1');
                } catch (\Exception $e) {
                    // Ignore si ça échoue
                }
            }

        } catch (\Exception $e) {
            $errors[] = "Erreur lors de la lecture du fichier: " . $e->getMessage();
        }

        return [
            'success' => empty($errors),
            'imported' => $imported,
            'deleted' => $deleted,
            'errors' => $errors
        ];
    }
}
