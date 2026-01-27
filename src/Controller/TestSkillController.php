<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestSkillController extends AbstractController
{
    #[Route('/test-skills', name: 'test_skills')]
    public function test(): Response
    {
        $testText = "Attack 1 enemy. Place [Vanish] on ally. Apply [DEF Up II] and [Stun] for 2 turns.";
        
        return $this->render('test_skill/index.html.twig', [ // ✅ Changé ici
            'testText' => $testText
        ]);
    }
}
