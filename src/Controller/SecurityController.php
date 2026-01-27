<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si déjà connecté, rediriger
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Récupère l'erreur de login
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        /* 🚀 PROTECTION PROD : 
           Si tu veux debugger sans polluer la prod, utilise ceci :
        */
        if ($this->getParameter('kernel.environment') === 'dev') {
            // Ces infos ne s'afficheront plus jamais sur oddonegaming.gg (ton serveur de prod)
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $heroesCount = $em->getRepository(\App\Entity\Heroes::class)->count([]);
        $dungeonsCount = $em->getRepository(\App\Entity\Dungeons::class)->count([]);
        $usersCount = $em->getRepository(User::class)->count([]);

        return $this->render('security/dashboard.html.twig', [
            'heroes_count' => $heroesCount,
            'dungeons_count' => $dungeonsCount,
            'users_count' => $usersCount,
        ]);
    }

    #[Route('/admin/users', name: 'app_admin_users')]
    #[IsGranted('ROLE_ADMIN')]
    public function users(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findBy([], ['createdAt' => 'DESC']);

        return $this->render('security/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/{id}/toggle-active', name: 'app_admin_user_toggle_active', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleUserActive(User $user, EntityManagerInterface $em): Response
    {
        $user->setIsActive(!$user->isActive());
        $em->flush();

        $this->addFlash('success', sprintf(
            'User %s has been %s',
            $user->getEmail(),
            $user->isActive() ? 'activated' : 'deactivated'
        ));

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/admin/users/{id}/make-admin', name: 'app_admin_user_make_admin', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function makeUserAdmin(User $user, EntityManagerInterface $em): Response
    {
        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);
            $em->flush();

            $this->addFlash('success', sprintf('User %s is now an admin', $user->getEmail()));
        }

        return $this->redirectToRoute('app_admin_users');
    }
}