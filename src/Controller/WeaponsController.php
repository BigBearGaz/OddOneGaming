<?php

namespace App\Controller;

use App\Entity\Weapons;
use App\Form\WeaponsType;
use App\Repository\WeaponsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/weapons')]
final class WeaponsController extends AbstractController
{
    #[Route(name: 'app_weapons_index', methods: ['GET'])]
    public function index(WeaponsRepository $weaponsRepository): Response
    {
        return $this->render('weapons/index.html.twig', [
            'weapons' => $weaponsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_weapons_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $weapon = new Weapons();
        $form = $this->createForm(WeaponsType::class, $weapon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($weapon);
            $entityManager->flush();

            return $this->redirectToRoute('app_weapons_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('weapons/new.html.twig', [
            'weapon' => $weapon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_weapons_show', methods: ['GET'])]
    public function show(Weapons $weapon): Response
    {
        return $this->render('weapons/show.html.twig', [
            'weapon' => $weapon,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_weapons_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Weapons $weapon, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WeaponsType::class, $weapon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_weapons_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('weapons/edit.html.twig', [
            'weapon' => $weapon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_weapons_delete', methods: ['POST'])]
    public function delete(Request $request, Weapons $weapon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$weapon->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($weapon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_weapons_index', [], Response::HTTP_SEE_OTHER);
    }
}
