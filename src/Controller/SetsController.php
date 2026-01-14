<?php

namespace App\Controller;

use App\Repository\SetsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SetsController extends AbstractController
{
    #[Route('/sets', name: 'app_sets')]
    public function index(SetsRepository $setsRepository): Response
    {
        // Récupère tous les sets sauf "Equipment Stats"
        $armorSets = $setsRepository->createQueryBuilder('s')
            ->where('s.name != :equipStats')
            ->setParameter('equipStats', 'Equipment Stats')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();

        // Récupère la ligne avec les stats d'équipement
        $equipmentStats = $setsRepository->findOneBy(['name' => 'Equipment Stats']);

        return $this->render('sets/index.html.twig', [
            'armor_sets' => $armorSets,
            'equipment_stats' => $equipmentStats,
        ]);
    }
}
