<?php

namespace App\Controller;

use App\Repository\EffectsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EffectsController extends AbstractController
{
    #[Route('/effects', name: 'app_effects')]
    public function index(EffectsRepository $effectsRepository): Response
    {
        // Récupère tous les effets groupés par type
        $buffs = $effectsRepository->findBy(['type' => 'buff'], ['name' => 'ASC']);
        $debuffs = $effectsRepository->findBy(['type' => 'debuff'], ['name' => 'ASC']);
        $disables = $effectsRepository->findBy(['type' => 'disable'], ['name' => 'ASC']);

        return $this->render('effects/index.html.twig', [
            'buffs' => $buffs,
            'debuffs' => $debuffs,
            'disables' => $disables,
        ]);
    }
}