<?php

namespace App\Controller;

use App\Entity\Sets;
use App\Form\SetsType;
use App\Repository\SetsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sets')]
final class SetsController extends AbstractController
{
    #[Route(name: 'app_sets_index', methods: ['GET'])]
    public function index(SetsRepository $setsRepository): Response
    {
        // Récupère tous les sets sauf "Equipment Stats"
        $armorSets = $setsRepository->createQueryBuilder('s')
            ->where('s.name != :equipStats')
            ->setParameter('equipStats', 'Equipment Stats')
            ->orderBy('s.baseName', 'ASC')
            ->addOrderBy('s.pieceType', 'ASC')
            ->getQuery()
            ->getResult();

        // Regroupe par baseName pour l'affichage (two, four, six)
        $groupedSets = [];
        foreach ($armorSets as $set) {
            $key = $set->getBaseName() ?? $set->getName();
            if (!isset($groupedSets[$key])) {
                $groupedSets[$key] = [
                    'name' => $set->getBaseName() ?? $set->getName(),
                    'imageUrl' => $set->getImageUrl(),
                    'two' => null,
                    'four' => null,
                    'six' => null,
                    'items' => [], // Pour les liens admin edit/delete
                ];
            }
            $pieceType = $set->getPieceType();
            if ($pieceType === 2) {
                $groupedSets[$key]['two'] = $set->getEffect();
            } elseif ($pieceType === 4) {
                $groupedSets[$key]['four'] = $set->getEffect();
            } elseif ($pieceType === 6) {
                $groupedSets[$key]['six'] = $set->getEffect();
            }
            $groupedSets[$key]['items'][] = $set;
        }
        $armorSetsDisplay = array_values($groupedSets);

        // Récupère la ligne avec les stats d'équipement
        $equipmentStats = $setsRepository->findOneBy(['name' => 'Equipment Stats']);

        return $this->render('sets/index.html.twig', [
            'armor_sets' => $armorSetsDisplay,
            'equipment_stats' => $equipmentStats,
        ]);
    }

    #[Route('/new', name: 'app_sets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $set = new Sets();
        $form = $this->createForm(SetsType::class, $set);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($set);
            $entityManager->flush();

            return $this->redirectToRoute('app_sets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sets/new.html.twig', [
            'set' => $set,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sets_show', methods: ['GET'])]
    public function show(Sets $set): Response
    {
        return $this->render('sets/show.html.twig', [
            'set' => $set,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sets $set, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SetsType::class, $set);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sets/edit.html.twig', [
            'set' => $set,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sets_delete', methods: ['POST'])]
    public function delete(Request $request, Sets $set, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$set->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($set);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sets_index', [], Response::HTTP_SEE_OTHER);
    }
}
