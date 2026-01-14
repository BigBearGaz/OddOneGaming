<?php

namespace App\Controller;

use App\Entity\Takeovers;
use App\Form\TakeoversType;
use App\Repository\TakeoversRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/takeovers')]
final class TakeoversController extends AbstractController
{
    #[Route(name: 'app_takeovers_index', methods: ['GET'])]
    public function index(TakeoversRepository $takeoversRepository): Response
    {
        return $this->render('takeovers/index.html.twig', [
            'takeovers' => $takeoversRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_takeovers_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $takeover = new Takeovers();
        $form = $this->createForm(TakeoversType::class, $takeover);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($takeover);
            $entityManager->flush();

            return $this->redirectToRoute('app_takeovers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('takeovers/new.html.twig', [
            'takeover' => $takeover,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_takeovers_show', methods: ['GET'])]
    public function show(Takeovers $takeover): Response
    {
        return $this->render('takeovers/show.html.twig', [
            'takeover' => $takeover,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_takeovers_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Takeovers $takeover, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TakeoversType::class, $takeover);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_takeovers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('takeovers/edit.html.twig', [
            'takeover' => $takeover,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_takeovers_delete', methods: ['POST'])]
    public function delete(Request $request, Takeovers $takeover, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$takeover->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($takeover);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_takeovers_index', [], Response::HTTP_SEE_OTHER);
    }
}
