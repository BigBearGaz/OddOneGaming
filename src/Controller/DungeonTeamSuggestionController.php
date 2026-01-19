<?php

namespace App\Controller;

use App\Entity\DungeonTeamSuggestion;
use App\Entity\Dungeons;
use App\Entity\Heroes;
use App\Form\DungeonTeamSuggestionType;
use App\Repository\DungeonTeamSuggestionRepository;
use App\Repository\HeroesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/team-suggestion')]
class DungeonTeamSuggestionController extends AbstractController
{
    #[Route('/', name: 'app_team_suggestion_index')]
    public function index(DungeonTeamSuggestionRepository $repository): Response
    {
        $suggestions = $repository->findAll();

        return $this->render('dungeon_team_suggestion/index.html.twig', [
            'suggestions' => $suggestions,
        ]);
    }

    #[Route('/dungeon/{id}/new', name: 'app_team_suggestion_new_for_dungeon')]
    public function newForDungeon(
        Request $request, 
        Dungeons $dungeon, 
        EntityManagerInterface $em,
        HeroesRepository $heroesRepo
    ): Response
    {
        $suggestion = new DungeonTeamSuggestion();
        $suggestion->setDungeon($dungeon);
        
        $form = $this->createForm(DungeonTeamSuggestionType::class, $suggestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les héros sélectionnés
            $heroIds = $request->request->all('dungeon_team_suggestion')['heroes'] ?? [];
            
            if (count($heroIds) < 3 || count($heroIds) > 5) {
                $this->addFlash('error', 'You must select between 3 and 5 heroes.');
            } else {
                // Ajouter les héros
                foreach ($heroIds as $heroId) {
                    $hero = $heroesRepo->find($heroId);
                    if ($hero) {
                        $suggestion->addHero($hero);
                    }
                }
                
                $em->persist($suggestion);
                $em->flush();

                $this->addFlash('success', 'Team suggestion created successfully!');
                return $this->redirectToRoute('app_dungeons_show', ['id' => $dungeon->getId()]);
            }
        }

        $allHeroes = $heroesRepo->findAll();

return $this->render('dungeon_team_suggestion/new.html.twig', [
            'form' => $form,
            'dungeon' => $dungeon,
            'allHeroes' => $allHeroes,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_team_suggestion_edit')]
    public function edit(
        Request $request, 
        DungeonTeamSuggestion $suggestion, 
        EntityManagerInterface $em,
        HeroesRepository $heroesRepo
    ): Response
    {
        $form = $this->createForm(DungeonTeamSuggestionType::class, $suggestion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les héros sélectionnés depuis la requête
            $formData = $request->request->all('dungeon_team_suggestion');
            $heroIds = $formData['heroes'] ?? [];
            
            if (count($heroIds) < 3 || count($heroIds) > 5) {
                $this->addFlash('error', 'You must select between 3 and 5 heroes.');
            } else {
                // Retirer tous les héros existants un par un
                foreach ($suggestion->getHeroes() as $hero) {
                    $suggestion->removeHero($hero);
                }
                
                // Ajouter les nouveaux héros sélectionnés
                foreach ($heroIds as $heroId) {
                    $hero = $heroesRepo->find($heroId);
                    if ($hero) {
                        $suggestion->addHero($hero);
                    }
                }
                
                $em->flush();

                $this->addFlash('success', 'Team suggestion updated successfully!');
                return $this->redirectToRoute('app_dungeons_show', ['id' => $suggestion->getDungeon()->getId()]);
            }
        }

        // Récupérer tous les héros pour l'affichage
        $allHeroes = $heroesRepo->findAll();

        return $this->render('dungeon_team_suggestion/edit.html.twig', [
            'form' => $form,
            'suggestion' => $suggestion,
            'allHeroes' => $allHeroes,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_team_suggestion_delete', methods: ['POST'])]
    public function delete(Request $request, DungeonTeamSuggestion $suggestion, EntityManagerInterface $em): Response
    {
        $dungeonId = $suggestion->getDungeon()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$suggestion->getId(), $request->request->get('_token'))) {
            $em->remove($suggestion);
            $em->flush();

            $this->addFlash('success', 'Team suggestion deleted.');
        }

        return $this->redirectToRoute('app_dungeons_show', ['id' => $dungeonId]);
    }
}
