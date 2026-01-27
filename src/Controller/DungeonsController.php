<?php

namespace App\Controller;

use App\Entity\Dungeons;
use App\Entity\DungeonPassive;
use App\Entity\DungeonPhase;
use App\Form\DungeonsType;
use App\Repository\DungeonTeamSuggestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dungeons')]
class DungeonsController extends AbstractController
{
    // ========== INDEX (Liste des donjons) ==========
    #[Route('/', name: 'app_dungeons_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $dungeons = $entityManager->getRepository(Dungeons::class)->findAll();

        return $this->render('dungeons/index.html.twig', [
            'dungeons' => $dungeons,
        ]);
    }

    // ========== NEW (Créer un donjon) ==========
    #[Route('/new', name: 'app_dungeons_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dungeon = new Dungeons();
        $form = $this->createForm(DungeonsType::class, $dungeon);
        $form->handleRequest($request);

        // 🔍 DÉBOGAGE
        if ($request->isMethod('POST')) {

            
            if ($form->isSubmitted() && !$form->isValid()) {
               
                foreach ($form->getErrors(true) as $error) {
                }
            }
            
        }

        if ($form->isSubmitted() && $form->isValid()) {
            
            
            $requestData = $request->request->all();
            
            // ✅ 1. TRAITER LES PASSIVES DE BASE (hors phases)
            if (isset($requestData['dungeons']['passives'])) {
                foreach ($requestData['dungeons']['passives'] as $passiveData) {
                    if (!empty($passiveData['name'])) {
                        $passive = new DungeonPassive();
                        $passive->setName($passiveData['name']);
                        $passive->setDescription($passiveData['description'] ?? '');
                        $passive->setPassiveOrder((int)($passiveData['passiveOrder'] ?? 1));
                        $passive->setDungeon($dungeon);
                        
                        $dungeon->addPassive($passive);
                        $entityManager->persist($passive);
                    }
                }
            }
            
            // ✅ 2. TRAITER LES PHASES et leurs PASSIVES
            if (isset($requestData['dungeons']['phases'])) {
               
                foreach ($requestData['dungeons']['phases'] as $phaseData) {
                    if (!empty($phaseData['name'])) {
                        $phase = new DungeonPhase();
                        $phase->setName($phaseData['name']);
                        $phase->setOrderNum((int)($phaseData['orderNum'] ?? 1));
                        
                        // Spell overrides (optionnel)
                        $phase->setSpell1NameOverride(!empty($phaseData['spell1NameOverride']) ? $phaseData['spell1NameOverride'] : null);
                        $phase->setSpell1DescriptionOverride(!empty($phaseData['spell1DescriptionOverride']) ? $phaseData['spell1DescriptionOverride'] : null);
                        $phase->setSpell2NameOverride(!empty($phaseData['spell2NameOverride']) ? $phaseData['spell2NameOverride'] : null);
                        $phase->setSpell2DescriptionOverride(!empty($phaseData['spell2DescriptionOverride']) ? $phaseData['spell2DescriptionOverride'] : null);
                        $phase->setSpell2CooldownOverride(
                            isset($phaseData['spell2CooldownOverride']) && $phaseData['spell2CooldownOverride'] !== '' 
                            ? (int)$phaseData['spell2CooldownOverride'] 
                            : null
                        );
                        $phase->setSpell3NameOverride(!empty($phaseData['spell3NameOverride']) ? $phaseData['spell3NameOverride'] : null);
                        $phase->setSpell3DescriptionOverride(!empty($phaseData['spell3DescriptionOverride']) ? $phaseData['spell3DescriptionOverride'] : null);
                        $phase->setSpell3CooldownOverride(
                            isset($phaseData['spell3CooldownOverride']) && $phaseData['spell3CooldownOverride'] !== '' 
                            ? (int)$phaseData['spell3CooldownOverride'] 
                            : null
                        );
                        
                        $phase->setDungeon($dungeon);
                        $dungeon->addPhase($phase);
                        
                        // Passives de cette phase
                        if (isset($phaseData['passives'])) {
                            foreach ($phaseData['passives'] as $passiveData) {
                                if (!empty($passiveData['name'])) {
                                    $passive = new DungeonPassive();
                                    $passive->setName($passiveData['name']);
                                    $passive->setDescription($passiveData['description'] ?? '');
                                    $passive->setPassiveOrder((int)($passiveData['passiveOrder'] ?? 1));
                                    $passive->setPhase($phase);
                                    
                                    $phase->addPassive($passive);
                                    $entityManager->persist($passive);
                                }
                            }
                        }
                        
                        $entityManager->persist($phase);
                    }
                }
            }
            
            try {
                $entityManager->persist($dungeon);
                $entityManager->flush();
                
              
                $this->addFlash('success', 'Donjon créé avec succès !');
                return $this->redirectToRoute('app_dungeons_index');
                
            } catch (\Exception $e) {
               
                $this->addFlash('error', 'Erreur: ' . $e->getMessage());
            }
        }

        return $this->render('dungeons/new.html.twig', [
            'form' => $form,
        ]);
    }

    // ========== EDIT (Éditer un donjon) ==========
    #[Route('/edit/{id}', name: 'app_dungeons_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Dungeons $dungeon, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DungeonsType::class, $dungeon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $requestData = $request->request->all();
            
            // ✅ Supprimer les anciennes passives de base
            foreach ($dungeon->getPassives() as $passive) {
                $entityManager->remove($passive);
            }
            $dungeon->getPassives()->clear();
            
            // ✅ Supprimer les anciennes phases et leurs passives
            foreach ($dungeon->getPhases() as $phase) {
                foreach ($phase->getPassives() as $passive) {
                    $entityManager->remove($passive);
                }
                $entityManager->remove($phase);
            }
            $dungeon->getPhases()->clear();
            
            // ✅ Recréer les PASSIVES DE BASE
            if (isset($requestData['dungeons']['passives'])) {
                foreach ($requestData['dungeons']['passives'] as $passiveData) {
                    if (!empty($passiveData['name'])) {
                        $passive = new DungeonPassive();
                        $passive->setName($passiveData['name']);
                        $passive->setDescription($passiveData['description'] ?? '');
                        $passive->setPassiveOrder((int)($passiveData['passiveOrder'] ?? 1));
                        $passive->setDungeon($dungeon);
                        
                        $dungeon->addPassive($passive);
                        $entityManager->persist($passive);
                    }
                }
            }
            
            // ✅ Recréer les PHASES et leurs PASSIVES
            if (isset($requestData['dungeons']['phases'])) {
                foreach ($requestData['dungeons']['phases'] as $phaseData) {
                    if (!empty($phaseData['name'])) {
                        $phase = new DungeonPhase();
                        $phase->setName($phaseData['name']);
                        $phase->setOrderNum((int)($phaseData['orderNum'] ?? 1));
                        
                        // Spell overrides
                        $phase->setSpell1NameOverride(!empty($phaseData['spell1NameOverride']) ? $phaseData['spell1NameOverride'] : null);
                        $phase->setSpell1DescriptionOverride(!empty($phaseData['spell1DescriptionOverride']) ? $phaseData['spell1DescriptionOverride'] : null);
                        $phase->setSpell2NameOverride(!empty($phaseData['spell2NameOverride']) ? $phaseData['spell2NameOverride'] : null);
                        $phase->setSpell2DescriptionOverride(!empty($phaseData['spell2DescriptionOverride']) ? $phaseData['spell2DescriptionOverride'] : null);
                        $phase->setSpell2CooldownOverride(
                            isset($phaseData['spell2CooldownOverride']) && $phaseData['spell2CooldownOverride'] !== '' 
                            ? (int)$phaseData['spell2CooldownOverride'] 
                            : null
                        );
                        $phase->setSpell3NameOverride(!empty($phaseData['spell3NameOverride']) ? $phaseData['spell3NameOverride'] : null);
                        $phase->setSpell3DescriptionOverride(!empty($phaseData['spell3DescriptionOverride']) ? $phaseData['spell3DescriptionOverride'] : null);
                        $phase->setSpell3CooldownOverride(
                            isset($phaseData['spell3CooldownOverride']) && $phaseData['spell3CooldownOverride'] !== '' 
                            ? (int)$phaseData['spell3CooldownOverride'] 
                            : null
                        );
                        
                        $phase->setDungeon($dungeon);
                        $dungeon->addPhase($phase);
                        
                        // Passives de cette phase
                        if (isset($phaseData['passives'])) {
                            foreach ($phaseData['passives'] as $passiveData) {
                                if (!empty($passiveData['name'])) {
                                    $passive = new DungeonPassive();
                                    $passive->setName($passiveData['name']);
                                    $passive->setDescription($passiveData['description'] ?? '');
                                    $passive->setPassiveOrder((int)($passiveData['passiveOrder'] ?? 1));
                                    $passive->setPhase($phase);
                                    
                                    $phase->addPassive($passive);
                                    $entityManager->persist($passive);
                                }
                            }
                        }
                        
                        $entityManager->persist($phase);
                    }
                }
            }
            
            $entityManager->flush();

            $this->addFlash('success', 'Donjon mis à jour avec succès !');
            return $this->redirectToRoute('app_dungeons_show', ['id' => $dungeon->getId()]);
        }

        return $this->render('dungeons/edit.html.twig', [
            'dungeon' => $dungeon,
            'form' => $form,
        ]);
    }

    // ========== DELETE (Supprimer un donjon) ==========
    #[Route('/delete/{id}', name: 'app_dungeons_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Dungeons $dungeon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dungeon->getId(), $request->request->get('_token'))) {
            $entityManager->remove($dungeon);
            $entityManager->flush();
            $this->addFlash('success', 'Donjon supprimé avec succès !');
        }

        return $this->redirectToRoute('app_dungeons_index');
    }

    // ========== SHOW (Afficher un donjon avec team suggestions) ==========
    #[Route('/{id}', name: 'app_dungeons_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Dungeons $dungeon, DungeonTeamSuggestionRepository $teamSuggestionRepo): Response
    {
        // Récupérer les team suggestions pour ce donjon
        $teamSuggestions = $teamSuggestionRepo->findBy(
            ['dungeon' => $dungeon],
            ['id' => 'ASC'] // Ordre par ID croissant
        );

        return $this->render('dungeons/show.html.twig', [
            'dungeon' => $dungeon,
            'teamSuggestions' => $teamSuggestions,
        ]);
    }
}
