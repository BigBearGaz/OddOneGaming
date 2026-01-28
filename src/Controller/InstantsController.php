<?php

namespace App\Controller;

use App\Entity\Instants;
use App\Form\InstantsType;
use App\Repository\InstantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/instants')]
final class InstantsController extends AbstractController
{
    #[Route(name: 'app_instants_index', methods: ['GET'])]
    public function index(InstantsRepository $instantsRepository): Response
    {
        return $this->render('instants/index.html.twig', [
            'instants' => $instantsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_instants_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $instant = new Instants();
        $form = $this->createForm(InstantsType::class, $instant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($instant);
            $entityManager->flush();

            return $this->redirectToRoute('app_instants_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instants/new.html.twig', [
            'instant' => $instant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instants_show', methods: ['GET'])]
    public function show(Instants $instant): Response
    {
        return $this->render('instants/show.html.twig', [
            'instant' => $instant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_instants_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Instants $instant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InstantsType::class, $instant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_instants_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instants/edit.html.twig', [
            'instant' => $instant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instants_delete', methods: ['POST'])]
    public function delete(Request $request, Instants $instant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$instant->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($instant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_instants_index', [], Response::HTTP_SEE_OTHER);
    }
}
