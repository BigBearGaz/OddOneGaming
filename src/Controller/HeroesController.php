<?php

namespace App\Controller;

use App\Entity\Heroes;
use App\Form\HeroesType;
use App\Repository\HeroesRepository;
use App\Repository\FactionRepository;
use App\Repository\TypeRepository;
use App\Repository\AffinityRepository;
use App\Repository\AllegianceRepository;
use App\Repository\RarityRepository;
use App\Repository\BuffsRepository;
use App\Repository\DebuffsRepository;
use App\Repository\DisableRepository;
use App\Repository\LeaderRepository;
use App\Repository\InstantsRepository; // Ajouté
use App\Service\ExcelExportService;
use App\Service\ExcelImportService;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/heroes')]
final class HeroesController extends AbstractController
{
    #[Route('/', name: 'app_heroes_index', methods: ['GET'])]
    public function index(
        Request $request,
        HeroesRepository $heroesRepository,
        FactionRepository $factionRepository,
        TypeRepository $typeRepository,
        AffinityRepository $affinityRepository,
        AllegianceRepository $allegianceRepository,
        RarityRepository $rarityRepository,
        BuffsRepository $buffsRepository,
        DebuffsRepository $debuffsRepository,
        DisableRepository $disableRepository,
        LeaderRepository $leaderRepository,
        InstantsRepository $instantsRepository // Ajouté
    ): Response {
        // Récupération des paramètres de filtrage
        $factionId = $request->query->get('faction');
        $typeId = $request->query->get('type');
        $affinityId = $request->query->get('affinity');
        $allegianceId = $request->query->get('allegiance');
        $rarityId = $request->query->get('rarity');
        $leaderId = $request->query->get('leader');
        $buffId = $request->query->get('buff');
        $debuffId = $request->query->get('debuff');
        $disableId = $request->query->get('disable');
        $instantId = $request->query->get('instant'); // Ajouté

        // Logique de filtrage
        if ($factionId || $typeId || $affinityId || $allegianceId || $rarityId || $leaderId || $buffId || $debuffId || $disableId || $instantId) {
            $heroes = $heroesRepository->findByFilters(
                factionId: $factionId,
                typeId: $typeId,
                affinityId: $affinityId,
                allegianceId: $allegianceId,
                rarityId: $rarityId,
                isLeader: $leaderId ? true : null,
                buffsIds: $buffId ? [$buffId] : null,
                debuffsIds: $debuffId ? [$debuffId] : null,
                disableIds: $disableId ? [$disableId] : null,
                instantIds: $instantId ? [$instantId] : null // Ajouté (pense à mettre à jour ton repository)
            );
        } else {
            $heroes = $heroesRepository->findAllWithRelations();
        }

        // Données pour remplir les listes de filtres (accordéons)
        $factions = $factionRepository->findBy([], ['name' => 'ASC']);
        $types = $typeRepository->findBy([], ['name' => 'ASC']);
        $affinities = $affinityRepository->findBy([], ['name' => 'ASC']);
        $allegiances = $allegianceRepository->findBy([], ['name' => 'ASC']);
        $rarities = $rarityRepository->findBy([], ['name' => 'ASC']);
        $leaders = $leaderRepository->findBy([], ['name' => 'ASC']);
        $buffsEntities = $buffsRepository->findBy([], ['name' => 'ASC']);
        $debuffsEntities = $debuffsRepository->findBy([], ['name' => 'ASC']);
        $disableEntities = $disableRepository->findBy([], ['name' => 'ASC']);
        $instants = $instantsRepository->findBy([], ['label' => 'ASC']); // Ajouté

        return $this->render('heroes/index.html.twig', [
            'heroes' => $heroes,
            'factions' => $factions,
            'types' => $types,
            'affinities' => $affinities,
            'allegiances' => $allegiances,
            'rarities' => $rarities,
            'leaders' => $leaders,
            'buffs' => $buffsEntities,
            'debuffs' => $debuffsEntities,
            'disables' => $disableEntities,
            'instants' => $instants, // Ajouté
            'currentFilters' => [
                'faction' => $factionId,
                'type' => $typeId,
                'affinity' => $affinityId,
                'allegiance' => $allegianceId,
                'rarity' => $rarityId,
                'leader' => $leaderId,
                'buff' => $buffId,
                'debuff' => $debuffId,
                'disable' => $disableId,
                'instant' => $instantId, // Ajouté
            ],
        ]);
    }

    #[Route('/effects', name: 'app_heroes_effects', methods: ['GET'])]
    public function effects(): Response
    {
        return $this->render('heroes/effect_heroes.html.twig');
    }

    #[Route('/export', name: 'app_heroes_export', methods: ['GET'])]
    public function export(ExcelExportService $excelExportService): Response
    {
        return $excelExportService->exportHeroes();
    }

    #[Route('/import', name: 'app_heroes_import', methods: ['GET', 'POST'])]
    public function import(Request $request, ExcelImportService $excelImportService): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('excel_file');

            if (!$file) {
                $this->addFlash('error', 'Aucun fichier sélectionné');
                return $this->redirectToRoute('app_heroes_import');
            }

            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, ['xlsx', 'xls'])) {
                $this->addFlash('error', 'Format de fichier invalide. Utilisez .xlsx ou .xls');
                return $this->redirectToRoute('app_heroes_import');
            }

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
    public function new(Request $request, EntityManagerInterface $entityManager, SlugService $slugService): Response
    {
        $hero = new Heroes();
        $form = $this->createForm(HeroesType::class, $hero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($hero->getSkillUpgrades() as $skillUpgrade) {
                $skillUpgrade->setHero($hero);
            }
            foreach ($hero->getAwakenings() as $awakening) {
                $awakening->setHero($hero);
            }

            $hero->setSlug($slugService->uniqueHeroSlug($hero->getName(), null));

            $entityManager->persist($hero);
            $entityManager->flush();

            $this->addFlash('success', 'Héros créé avec succès !');

            return $this->redirectToRoute('app_heroes_show', ['slug' => $hero->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('heroes/new.html.twig', [
            'hero' => $hero,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_heroes_show', methods: ['GET'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Heroes $hero
    ): Response {
        return $this->render('heroes/show.html.twig', [
            'hero' => $hero,
        ]);
    }

    #[Route('/{slug}/edit', name: 'app_heroes_edit', methods: ['GET', 'POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Heroes $hero,
        EntityManagerInterface $entityManager,
        SlugService $slugService
    ): Response {
        $form = $this->createForm(HeroesType::class, $hero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($hero->getSkillUpgrades() as $skillUpgrade) {
                $skillUpgrade->setHero($hero);
            }
            foreach ($hero->getAwakenings() as $awakening) {
                $awakening->setHero($hero);
            }

            $hero->setSlug($slugService->uniqueHeroSlug($hero->getName(), $hero->getId()));

            $entityManager->flush();

            $this->addFlash('success', 'Héros modifié avec succès !');

            return $this->redirectToRoute('app_heroes_show', ['slug' => $hero->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('heroes/edit.html.twig', [
            'hero' => $hero,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_heroes_delete', methods: ['POST'], requirements: ['slug' => Requirement::ASCII_SLUG])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['slug' => 'slug'])] Heroes $hero,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $hero->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($hero);
            $entityManager->flush();
            $this->addFlash('success', 'Héros supprimé avec succès !');
        }

        return $this->redirectToRoute('app_heroes_index', [], Response::HTTP_SEE_OTHER);
    }
}