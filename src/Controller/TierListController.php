<?php

namespace App\Controller;

use App\Entity\HeroTierList;
use App\Entity\TierListMode;
use App\Repository\HeroTierListRepository;
use App\Repository\HeroesRepository;
use App\Repository\TierListModeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tier-list')]
class TierListController extends AbstractController
{
    private const TIERS       = ['SSS', 'SS', 'S', 'A', 'B', 'C', 'D'];
    private const TIER_SCORES = ['SSS' => 7, 'SS' => 6, 'S' => 5, 'A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];

    // INDEX → classement par score cumulé (héros × modes)
    #[Route('/', name: 'app_tier_list_index')]
    public function index(HeroTierListRepository $tierListRepo, TierListModeRepository $modeRepo): Response
    {
        $modes    = $modeRepo->findBy([], ['sortOrder' => 'ASC']);
        $entries  = $tierListRepo->findAllWithHeroes();
        $maxScore = count($modes) * max(self::TIER_SCORES);

        $matrix = [];
        foreach ($entries as $entry) {
            $hero = $entry->getHero();
            $id   = $hero->getId();
            if (!isset($matrix[$id])) {
                $matrix[$id] = ['hero' => $hero, 'grades' => [], 'score' => 0];
            }
            $slug  = $entry->getCategory();
            $tier  = $entry->getTier();
            $matrix[$id]['grades'][$slug]  = $tier;
            $matrix[$id]['score']         += self::TIER_SCORES[$tier] ?? 0;
        }

        // Tri décroissant par score, puis alphabétique à égalité
        usort($matrix, function ($a, $b) {
            if ($b['score'] !== $a['score']) return $b['score'] - $a['score'];
            return strcmp($a['hero']->getName(), $b['hero']->getName());
        });

        foreach ($matrix as &$row) {
            $row['grade'] = $this->scoreToGrade($row['score'], $maxScore);
        }
        unset($row);

        return $this->render('tier_list/matrix.html.twig', [
            'modes'    => $modes,
            'matrix'   => $matrix,
            'maxScore' => $maxScore,
        ]);
    }

    private function scoreToGrade(int $score, int $maxScore): string
    {
        if ($maxScore === 0) return 'D';
        $pct = $score / $maxScore;
        if ($pct >= 0.86) return 'SSS';
        if ($pct >= 0.72) return 'SS';
        if ($pct >= 0.57) return 'S';
        if ($pct >= 0.43) return 'A';
        if ($pct >= 0.29) return 'B';
        if ($pct >= 0.14) return 'C';
        return 'D';
    }

    // MATRIX EDIT — grille cliquable admin (toutes les heroes × tous les modes)
    #[Route('/edit', name: 'app_tier_list_matrix_edit', methods: ['GET'])]
    public function matrixEdit(
        HeroTierListRepository $tierListRepo,
        HeroesRepository $heroesRepo,
        TierListModeRepository $modeRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $modes   = $modeRepo->findBy([], ['sortOrder' => 'ASC']);
        $entries = $tierListRepo->findAllWithHeroes();
        $heroes  = $heroesRepo->findAll();

        // entryMap[heroId][modeSlug] = ['tier' => ..., 'entryId' => ...]
        $entryMap = [];
        foreach ($entries as $entry) {
            $hid  = $entry->getHero()->getId();
            $slug = $entry->getCategory();
            $entryMap[$hid][$slug] = ['tier' => $entry->getTier(), 'entryId' => $entry->getId()];
        }

        usort($heroes, fn($a, $b) => strcmp($a->getName() ?? '', $b->getName() ?? ''));

        return $this->render('tier_list/matrix_edit.html.twig', [
            'modes'    => $modes,
            'heroes'   => $heroes,
            'entryMap' => $entryMap,
        ]);
    }

    // SHOW — vue par mode (bouton "Voir" depuis edit)
    #[Route('/{slug}', name: 'app_tier_list_show', methods: ['GET'])]
    public function show(string $slug, HeroTierListRepository $tierListRepo, TierListModeRepository $modeRepo): Response
    {
        $mode = $modeRepo->findOneBy(['slug' => $slug]);
        if (!$mode) {
            throw $this->createNotFoundException("Mode '$slug' introuvable.");
        }

        $groupedModes = $modeRepo->findAllGrouped();
        $tierData     = $this->buildTierData($tierListRepo, $slug);

        return $this->render('tier_list/show.html.twig', [
            'mode'         => $mode,
            'groupedModes' => $groupedModes,
            'tierData'     => $tierData,
        ]);
    }

    // EDIT — admin drag & drop pour un mode
    #[Route('/{slug}/edit', name: 'app_tier_list_edit', methods: ['GET'])]
    public function edit(string $slug, HeroTierListRepository $tierListRepo, HeroesRepository $heroesRepo, TierListModeRepository $modeRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $mode = $modeRepo->findOneBy(['slug' => $slug]);
        if (!$mode) {
            throw $this->createNotFoundException("Mode '$slug' introuvable.");
        }

        $groupedModes = $modeRepo->findAllGrouped();
        $tierData     = $this->buildTierData($tierListRepo, $slug);

        $heroesInTierList = [];
        foreach ($tierData as $heroes) {
            foreach ($heroes as $entry) {
                $heroesInTierList[] = $entry->getHero()->getId();
            }
        }

        $allHeroes      = $heroesRepo->findAll();
        $unrankedHeroes = array_filter($allHeroes, fn($h) => !in_array($h->getId(), $heroesInTierList));

        return $this->render('tier_list/edit.html.twig', [
            'mode'          => $mode,
            'groupedModes'  => $groupedModes,
            'tierData'      => $tierData,
            'unrankedHeroes'=> array_values($unrankedHeroes),
        ]);
    }

    // API — Ajouter un héros à un mode
    #[Route('/api/add-hero', name: 'app_tier_list_api_add', methods: ['POST'])]
    public function addHero(Request $request, EntityManagerInterface $em, HeroesRepository $heroesRepo): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data     = json_decode($request->getContent(), true);
        $heroId   = $data['heroId']   ?? null;
        $tier     = $data['tier']     ?? null;
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

    // API — Retirer un héros
    #[Route('/api/remove-hero/{id}', name: 'app_tier_list_api_remove', methods: ['DELETE'])]
    public function removeHero(HeroTierList $entry, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($entry);
        $em->flush();
        return $this->json(['success' => true]);
    }

    // API — Mettre à jour l'ordre / tier
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

    // ───────────────────────────────────────────────
    private function buildTierData(HeroTierListRepository $repo, string $slug): array
    {
        $tierData = array_fill_keys(self::TIERS, []);
        $entries  = $repo->findBy(['category' => $slug], ['tier' => 'ASC', 'rankingOrder' => 'ASC']);
        foreach ($entries as $entry) {
            $t = $entry->getTier();
            if (isset($tierData[$t])) {
                $tierData[$t][] = $entry;
            }
        }
        return $tierData;
    }
}
