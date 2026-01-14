<?php

namespace App\Controller;

use App\Entity\Dungeons;
use App\Form\DungeonsType;
use App\Repository\DungeonsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dungeons')]
final class DungeonsController extends AbstractController
{
    #[Route(name: 'app_dungeons_index', methods: ['GET'])]
    public function index(DungeonsRepository $dungeonsRepository): Response
    {
        return $this->render('dungeons/index.html.twig', [
            'dungeons' => $dungeonsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dungeons_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dungeon = new Dungeons();
        $form = $this->createForm(DungeonsType::class, $dungeon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dungeon);
            $entityManager->flush();

            return $this->redirectToRoute('app_dungeons_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dungeons/new.html.twig', [
            'dungeon' => $dungeon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dungeons_show', methods: ['GET'])]
    public function show(Dungeons $dungeon): Response
    {
        return $this->render('dungeons/show.html.twig', [
            'dungeon' => $dungeon,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dungeons_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Dungeons $dungeon, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DungeonsType::class, $dungeon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dungeons_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dungeons/edit.html.twig', [
            'dungeon' => $dungeon,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dungeons_delete', methods: ['POST'])]
    public function delete(Request $request, Dungeons $dungeon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dungeon->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($dungeon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dungeons_index', [], Response::HTTP_SEE_OTHER);
    }
}
