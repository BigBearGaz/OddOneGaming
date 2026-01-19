<?php

namespace App\Controller;

use App\Entity\HeroTierList;
use App\Repository\HeroTierListRepository;
use App\Repository\HeroesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tier-list')]
class TierListController extends AbstractController
{
    // INDEX - Liste toutes les catégories de tier lists
    #[Route('/', name: 'app_tier_list_index')]
    public function index(HeroTierListRepository $tierListRepo): Response
    {
        // Récupérer toutes les catégories avec le nombre de héros
        $categories = $tierListRepo->createQueryBuilder('t')
            ->select('t.category, COUNT(t.id) as heroCount')
            ->groupBy('t.category')
            ->orderBy('t.category', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('tier_list/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    // SHOW - Affichage public d'une tier list
    #[Route('/{category}', name: 'app_tier_list_show', methods: ['GET'])]
    public function show(string $category, HeroTierListRepository $tierListRepo): Response
    {
        // Récupérer toutes les catégories pour la navigation
        $allCategories = $tierListRepo->createQueryBuilder('t')
            ->select('DISTINCT t.category')
            ->orderBy('t.category', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        // Récupérer les héros de cette catégorie
        $tierListEntries = $tierListRepo->findBy(
            ['category' => $category],
            ['tier' => 'ASC', 'rankingOrder' => 'ASC']
        );

        // Organiser par tier
        $tierData = [
            'S' => [],
            'A' => [],
            'B' => [],
            'C' => [],
            'D' => [],
            'F' => [],
        ];

        foreach ($tierListEntries as $entry) {
            $tier = $entry->getTier();
            if (isset($tierData[$tier])) {
                $tierData[$tier][] = $entry;
            }
        }

        return $this->render('tier_list/show.html.twig', [
            'category' => $category,
            'allCategories' => $allCategories,
            'tierData' => $tierData,
        ]);
    }

    // NEW - Créer une nouvelle tier list (catégorie)
    #[Route('/new/create', name: 'app_tier_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($request->isMethod('POST')) {
            $categoryName = $request->request->get('categoryName');
            
            if ($categoryName) {
                $this->addFlash('success', "Category '$categoryName' created successfully!");
                return $this->redirectToRoute('app_tier_list_edit', ['category' => $categoryName]);
            } else {
                $this->addFlash('error', 'Please enter a category name.');
            }
        }

        return $this->render('tier_list/new.html.twig');
    }

    // EDIT - Éditer une tier list avec drag & drop
    #[Route('/{category}/edit', name: 'app_tier_list_edit', methods: ['GET'])]
    public function edit(
        string $category, 
        HeroTierListRepository $tierListRepo,
        HeroesRepository $heroesRepo
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupérer toutes les catégories
        $allCategories = $tierListRepo->createQueryBuilder('t')
            ->select('DISTINCT t.category')
            ->orderBy('t.category', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();

        // Récupérer tous les héros
        $allHeroes = $heroesRepo->findAll();

        // Récupérer les héros déjà dans la tier list
        $tierListEntries = $tierListRepo->findBy(
            ['category' => $category],
            ['tier' => 'ASC', 'rankingOrder' => 'ASC']
        );

        // Organiser par tier
        $tierData = [
            'S' => [],
            'A' => [],
            'B' => [],
            'C' => [],
            'D' => [],
            'F' => [],
        ];

        $heroesInTierList = [];
        foreach ($tierListEntries as $entry) {
            $tier = $entry->getTier();
            if (isset($tierData[$tier])) {
                $tierData[$tier][] = $entry;
            }
            $heroesInTierList[] = $entry->getHero()->getId();
        }

        // Héros non classés
        $unrankedHeroes = array_filter($allHeroes, function($hero) use ($heroesInTierList) {
            return !in_array($hero->getId(), $heroesInTierList);
        });

        return $this->render('tier_list/edit.html.twig', [
            'category' => $category,
            'allCategories' => $allCategories,
            'tierData' => $tierData,
            'unrankedHeroes' => $unrankedHeroes,
        ]);
    }

    // DELETE - Supprimer une catégorie entière
    #[Route('/{category}/delete', name: 'app_tier_list_delete', methods: ['POST'])]
    public function delete(string $category, Request $request, EntityManagerInterface $em, HeroTierListRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$category, $request->request->get('_token'))) {
            $entries = $repo->findBy(['category' => $category]);
            
            foreach ($entries as $entry) {
                $em->remove($entry);
            }
            
            $em->flush();

            $this->addFlash('success', "Tier list '$category' deleted successfully!");
        }

        return $this->redirectToRoute('app_tier_list_index');
    }

    // API - Ajouter un héros à une tier list
    #[Route('/api/add-hero', name: 'app_tier_list_api_add', methods: ['POST'])]
    public function addHero(Request $request, EntityManagerInterface $em, HeroesRepository $heroesRepo): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);
        
        $heroId = $data['heroId'] ?? null;
        $tier = $data['tier'] ?? null;
        $category = $data['category'] ?? null;

        if (!$heroId || !$tier || !$category) {
            return $this->json(['error' => 'Missing parameters'], 400);
        }

        $hero = $heroesRepo->find($heroId);
        if (!$hero) {
            return $this->json(['error' => 'Hero not found'], 404);
        }

        $entry = new HeroTierList();
        $entry->setHero($hero);
        $entry->setTier($tier);
        $entry->setCategory($category);
        $entry->setRankingOrder(999);
        $entry->setUpdatedAt(new \DateTime());

        $em->persist($entry);
        $em->flush();

        return $this->json(['success' => true, 'entryId' => $entry->getId()]);
    }

    // API - Retirer un héros
    #[Route('/api/remove-hero/{id}', name: 'app_tier_list_api_remove', methods: ['DELETE'])]
    public function removeHero(HeroTierList $entry, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($entry);
        $em->flush();

        return $this->json(['success' => true]);
    }

    // API - Mettre à jour l'ordre
    #[Route('/api/update-order', name: 'app_tier_list_api_update_order', methods: ['POST'])]
    public function updateOrder(Request $request, EntityManagerInterface $em, HeroTierListRepository $repo): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);
        
        foreach ($data as $update) {
            $entry = $repo->find($update['id']);
            if ($entry) {
                $entry->setTier($update['tier']);
                $entry->setRankingOrder($update['order']);
                $entry->setUpdatedAt(new \DateTime());
            }
        }

        $em->flush();

        return $this->json(['success' => true]);
    }
}
