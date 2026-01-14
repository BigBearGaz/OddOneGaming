<?php

namespace App\Service;

use App\Repository\HeroesRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportService
{
    private HeroesRepository $heroesRepository;

    public function __construct(HeroesRepository $heroesRepository)
    {
        $this->heroesRepository = $heroesRepository;
    }

    public function exportHeroes(): StreamedResponse
    {
        $response = new StreamedResponse();
        
        $response->setCallback(function () {
            // Récupère tous les héros
            $heroes = $this->heroesRepository->findAll();
            
            // Crée un nouveau spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Heroes');
            
            // Définit les en-têtes
            $headers = [
                'ID', 'Nom', 'Faction', 'Type', 'Affinity', 'Allegiance', 
                'Weapon 1', 'Weapon 2', 'Leader', 'Base Skill', 'Core Skill', 
                'Ultimate', 'Passive', 'Imprint General', 'Imprint Level 1', 
                'Imprint Level 2', 'Imprint Level 3', 'Image URL', 'Video URL'
            ];
            
            $sheet->fromArray($headers, null, 'A1');
            
            // Style des en-têtes
            $headerStyle = $sheet->getStyle('A1:S1');
            $headerStyle->getFont()->setBold(true)->setSize(12);
            $headerStyle->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4CAF50');
            $headerStyle->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            
            // Remplit les données
            $row = 2;
            foreach ($heroes as $hero) {
                $sheet->fromArray([
                    $hero->getId(),
                    $hero->getName(),
                    $hero->getFaction(),
                    $hero->getType(),
                    $hero->getAffinity(),
                    $hero->getAllegiance(),
                    $hero->getWeapons1(),
                    $hero->getWeapons2(),
                    $hero->getLeader(),
                    $hero->getBase(),
                    $hero->getCore(),
                    $hero->getUltimate(),
                    $hero->getPassive(),
                    $hero->getImprint(),
                    $hero->getImprint1(),
                    $hero->getImprint2(),
                    $hero->getImprint3(),
                    $hero->getImageUrl(),
                    $hero->getVideosUrl(),
                ], null, 'A' . $row);
                $row++;
            }
            
            // Auto-ajuste les colonnes
            foreach (range('A', 'S') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Ajoute des filtres sur la première ligne
            $sheet->setAutoFilter('A1:S1');
            
            // Crée le writer et sauvegarde
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            
            // Libère la mémoire
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        });
        
        // Configure les en-têtes HTTP
        $filename = 'heroes_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }
}
