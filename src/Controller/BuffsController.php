<?php

namespace App\Controller;

use App\Entity\Buffs;
use App\Form\BuffsType;
use App\Repository\BuffsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/buffs')]
final class BuffsController extends AbstractController
{
    #[Route(name: 'app_buffs_index', methods: ['GET'])]
    public function index(BuffsRepository $buffsRepository): Response
    {
        return $this->render('buffs/index.html.twig', [
            'buffs' => $buffsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_buffs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $buff = new Buffs();
        $form = $this->createForm(BuffsType::class, $buff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($buff);
            $entityManager->flush();

            return $this->redirectToRoute('app_buffs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('buffs/new.html.twig', [
            'buff' => $buff,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_buffs_show', methods: ['GET'])]
    public function show(Buffs $buff): Response
    {
        return $this->render('buffs/show.html.twig', [
            'buff' => $buff,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_buffs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Buffs $buff, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BuffsType::class, $buff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_buffs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('buffs/edit.html.twig', [
            'buff' => $buff,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_buffs_delete', methods: ['POST'])]
    public function delete(Request $request, Buffs $buff, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$buff->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($buff);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_buffs_index', [], Response::HTTP_SEE_OTHER);
    }
}
