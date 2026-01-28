<?php

namespace App\Controller;

use App\Entity\Debuffs;
use App\Form\DebuffsType;
use App\Repository\DebuffsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/debuffs')]
final class DebuffsController extends AbstractController
{
    #[Route(name: 'app_debuffs_index', methods: ['GET'])]
    public function index(DebuffsRepository $debuffsRepository): Response
    {
        return $this->render('debuffs/index.html.twig', [
            'debuffs' => $debuffsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_debuffs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $debuff = new Debuffs();
        $form = $this->createForm(DebuffsType::class, $debuff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($debuff);
            $entityManager->flush();

            return $this->redirectToRoute('app_debuffs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('debuffs/new.html.twig', [
            'debuff' => $debuff,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_debuffs_show', methods: ['GET'])]
    public function show(Debuffs $debuff): Response
    {
        return $this->render('debuffs/show.html.twig', [
            'debuff' => $debuff,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_debuffs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Debuffs $debuff, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DebuffsType::class, $debuff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_debuffs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('debuffs/edit.html.twig', [
            'debuff' => $debuff,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_debuffs_delete', methods: ['POST'])]
    public function delete(Request $request, Debuffs $debuff, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$debuff->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($debuff);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_debuffs_index', [], Response::HTTP_SEE_OTHER);
    }
}
