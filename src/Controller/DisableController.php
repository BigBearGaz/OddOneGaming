<?php

namespace App\Controller;

use App\Entity\Disable;
use App\Form\DisableType;
use App\Repository\DisableRepository;
use App\Service\EffectGrouperService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/disable')]
final class DisableController extends AbstractController
{
    #[Route(name: 'app_disable_index', methods: ['GET'])]
    public function index(EffectGrouperService $grouper): Response
    {
        return $this->render('effects/category.html.twig', [
            'type'   => 'disable',
            'groups' => $grouper->getGroups('disable'),
        ]);
    }

    #[Route('/new', name: 'app_disable_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $disable = new Disable();
        $form = $this->createForm(DisableType::class, $disable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($disable);
            $entityManager->flush();

            return $this->redirectToRoute('app_disable_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('disable/new.html.twig', [
            'disable' => $disable,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_disable_show', methods: ['GET'])]
    public function show(Disable $disable): Response
    {
        return $this->render('disable/show.html.twig', [
            'disable' => $disable,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_disable_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Disable $disable, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DisableType::class, $disable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_disable_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('disable/edit.html.twig', [
            'disable' => $disable,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_disable_delete', methods: ['POST'])]
    public function delete(Request $request, Disable $disable, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$disable->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($disable);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_disable_index', [], Response::HTTP_SEE_OTHER);
    }
}
