<?php

namespace App\Controller;

use App\Entity\Heroes;
use App\Form\HeroesType;
use App\Repository\HeroesRepository;
use App\Service\ExcelExportService;
use App\Service\ExcelImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/heroes')]
final class HeroesController extends AbstractController
{
    #[Route('/', name: 'app_heroes_index', methods: ['GET'])]
    public function index(Request $request, HeroesRepository $heroesRepository): Response
    {
        // Récupère les paramètres de filtre
        $faction = $request->query->get('faction');
        $type = $request->query->get('type');
        $affinity = $request->query->get('affinity');
        $allegiance = $request->query->get('allegiance');
        $isLeader = $request->query->get('leader');
        $buffs = $request->query->get('buffs');
        $debuffs = $request->query->get('debuffs');
        $disable = $request->query->get('disable');

        // Récupère les héros filtrés
        $heroes = $heroesRepository->findByFilters(
            $faction, 
            $type, 
            $affinity, 
            $allegiance, 
            $isLeader,
            $buffs,
            $debuffs,
            $disable
        );

        // Récupère les valeurs uniques pour les filtres simples
        $factions = $heroesRepository->findDistinctValues('faction');
        $types = $heroesRepository->findDistinctValues('type');
        $affinities = $heroesRepository->findDistinctValues('affinity');
        $allegiances = $heroesRepository->findDistinctValues('allegiance');
        
        // Récupère les valeurs uniques pour les champs séparés par virgule
        $buffsOptions = $heroesRepository->findDistinctValuesFromCommaSeparated('buffs');
        $debuffsOptions = $heroesRepository->findDistinctValuesFromCommaSeparated('debuffs');
        $disableOptions = $heroesRepository->findDistinctValuesFromCommaSeparated('disable');

        // Extraire les TYPES de leaders (Defense, Speed, Attack, etc.)
        $allHeroes = $heroesRepository->findAll();
        $leaderTypes = [];
        
        foreach ($allHeroes as $hero) {
            if ($hero->getLeader()) {
                $leaderText = strtolower($hero->getLeader());
                
                // Détecter le type de leader
                if (stripos($leaderText, 'speed') !== false) {
                    $leaderTypes['Speed'] = true;
                } elseif (stripos($leaderText, 'defense') !== false) {
                    $leaderTypes['Defense'] = true;
                } elseif (stripos($leaderText, 'attack') !== false) {
                    $leaderTypes['Attack'] = true;
                } elseif (stripos($leaderText, 'hp') !== false) {
                    $leaderTypes['Health'] = true;
                } elseif (stripos($leaderText, 'initiative') !== false || stripos($leaderText, 'init') !== false) {
                    $leaderTypes['Initiative'] = true;
                } elseif (stripos($leaderText, 'resistance') !== false) {
                    $leaderTypes['Resistance'] = true;
                } elseif (stripos($leaderText, 'accuracy') !== false) {
                    $leaderTypes['Accuracy'] = true;
                } elseif (stripos($leaderText, 'critical rate') !== false || stripos($leaderText, 'crit rate') !== false) {
                    $leaderTypes['Crit Rate'] = true;
                } elseif (stripos($leaderText, 'critical damage') !== false || stripos($leaderText, 'crit damage') !== false) {
                    $leaderTypes['Crit Damage'] = true;
                }
            }
        }
        
        $leaderTypes = array_keys($leaderTypes);
        sort($leaderTypes);

        return $this->render('heroes/index.html.twig', [
            'heroes' => $heroes,
            'factions' => $factions,
            'types' => $types,
            'affinities' => $affinities,
            'allegiances' => $allegiances,
            'buffs' => $buffsOptions,
            'debuffs' => $debuffsOptions,
            'disables' => $disableOptions,
            'leaderTypes' => $leaderTypes,
            'currentFilters' => [
                'faction' => $faction,
                'type' => $type,
                'affinity' => $affinity,
                'allegiance' => $allegiance,
                'leader' => $isLeader,
                'buffs' => $buffs,
                'debuffs' => $debuffs,
                'disable' => $disable,
            ]
        ]);
    }

    // NOUVELLE ROUTE - Page des effets
    #[Route('/effects', name: 'app_heroes_effects', methods: ['GET'])]
    public function effects(): Response
    {
        return $this->render('heroes/effect_heroes.html.twig');
    }

    // EXPORT EXCEL
    #[Route('/export', name: 'app_heroes_export', methods: ['GET'])]
    public function export(ExcelExportService $excelExportService): Response
    {
        return $excelExportService->exportHeroes();
    }

    // IMPORT EXCEL
    #[Route('/import', name: 'app_heroes_import', methods: ['GET', 'POST'])]
    public function import(Request $request, ExcelImportService $excelImportService): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('excel_file');

            if (!$file) {
                $this->addFlash('error', 'Aucun fichier sélectionné');
                return $this->redirectToRoute('app_heroes_import');
            }

            // Vérifie l'extension
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, ['xlsx', 'xls'])) {
                $this->addFlash('error', 'Format de fichier invalide. Utilisez .xlsx ou .xls');
                return $this->redirectToRoute('app_heroes_import');
            }

            // Import avec suppression
            $result = $excelImportService->importHeroes($file->getPathname(), true);

            if ($result['success']) {
                $this->addFlash('success', sprintf(
                    '✅ %d anciens héros supprimés, %d nouveaux héros importés !',
                    $result['deleted'],
                    $result['imported']
                ));
            } else {
                $this->addFlash('error', 'Erreurs lors de l\'import : ' . implode(', ', $result['errors']));
            }

            return $this->redirectToRoute('app_heroes_index');
        }

        return $this->render('heroes/import.html.twig');
    }

    #[Route('/new', name: 'app_heroes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hero = new Heroes();
        $form = $this->createForm(HeroesType::class, $hero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hero);
            $entityManager->flush();

            return $this->redirectToRoute('app_heroes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('heroes/new.html.twig', [
            'hero' => $hero,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_heroes_show', methods: ['GET'])]
    public function show(Heroes $hero): Response
    {
        return $this->render('heroes/show.html.twig', [
            'hero' => $hero,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_heroes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Heroes $hero, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HeroesType::class, $hero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_heroes_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('heroes/edit.html.twig', [
            'hero' => $hero,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_heroes_delete', methods: ['POST'])]
    public function delete(Request $request, Heroes $hero, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hero->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($hero);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_heroes_index', [], Response::HTTP_SEE_OTHER);
    }
}
