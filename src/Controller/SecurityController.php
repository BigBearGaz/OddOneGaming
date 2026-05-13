<?php
// src/Controller/SecurityController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SecurityController extends AbstractController
{
    #[Route('/oog-panel-7x9k', name: 'app_login')]
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
        $heroesCount   = $em->getRepository(\App\Entity\Heroes::class)->count([]);
        $dungeonsCount = $em->getRepository(\App\Entity\Dungeons::class)->count([]);
        $usersCount    = $em->getRepository(User::class)->count([]);
        $weaponsCount  = $em->getRepository(\App\Entity\Weapons::class)->count([]);
        $imprintsCount = $em->getRepository(\App\Entity\Imprints::class)->count([]);
        $setsCount     = $em->getRepository(\App\Entity\Sets::class)->count([]);

        $stateFile = $this->getParameter('kernel.project_dir') . '/var/sync_state.json';
        $state     = file_exists($stateFile) ? (json_decode(file_get_contents($stateFile), true) ?? []) : [];

        // Compat ancien fichier heroes-only
        if (empty($state) && file_exists($this->getParameter('kernel.project_dir') . '/var/ravenpyros_sync_state.json')) {
            $old = json_decode(file_get_contents($this->getParameter('kernel.project_dir') . '/var/ravenpyros_sync_state.json'), true) ?? [];
            $state['heroes']['last_run'] = $old['synced_at'] ?? null;
        }

        return $this->render('security/dashboard.html.twig', [
            'heroes_count'   => $heroesCount,
            'dungeons_count' => $dungeonsCount,
            'users_count'    => $usersCount,
            'weapons_count'  => $weaponsCount,
            'imprints_count' => $imprintsCount,
            'sets_count'     => $setsCount,
            'sync_state'     => $state,
        ]);
    }

    #[Route('/admin/sync', name: 'app_admin_sync', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function sync(Request $request, KernelInterface $kernel): Response
    {
        $action = $request->request->get('action', 'all');

        if (!$this->isCsrfTokenValid('sync_' . $action, $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        set_time_limit(600);

        $app = new Application($kernel);
        $app->setAutoExit(false);
        $output = new BufferedOutput();

        $jobs = match ($action) {
            'heroes'  => ['Heroes'  => ['command' => 'app:sync-heroes-api']],
            'catalog' => ['Catalog' => ['command' => 'app:sync-catalog-api']],
            'images'  => ['Images'  => ['command' => 'app:scrape-hero-images']],
            default   => [
                'Heroes'  => ['command' => 'app:sync-heroes-api'],
                'Catalog' => ['command' => 'app:sync-catalog-api'],
            ],
        };

        $errors = [];
        foreach ($jobs as $label => $args) {
            $code = $app->run(new ArrayInput($args), $output);
            if ($code !== 0) {
                $errors[] = $label;
            }
        }

        // Persist state
        $stateFile = $this->getParameter('kernel.project_dir') . '/var/sync_state.json';
        $state     = file_exists($stateFile) ? (json_decode(file_get_contents($stateFile), true) ?? []) : [];
        $now       = (new \DateTimeImmutable())->format(\DateTime::ATOM);
        foreach (array_keys($jobs) as $label) {
            $key          = strtolower($label);
            $state[$key]  = ['last_run' => $now, 'status' => in_array($label, $errors) ? 'error' : 'ok'];
        }
        file_put_contents($stateFile, json_encode($state, JSON_PRETTY_PRINT));

        if (empty($errors)) {
            $labels = implode(', ', array_keys($jobs));
            $this->addFlash('success', "✅ Sync complete — {$labels} updated successfully.");
        } else {
            $this->addFlash('error', '⚠️ Partial sync — errors on: ' . implode(', ', $errors));
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    // Keep old route for backwards compat
    #[Route('/admin/sync-api', name: 'app_admin_sync_api', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function syncApiLegacy(Request $request, KernelInterface $kernel): Response
    {
        $request->request->set('action', 'all');
        return $this->sync($request, $kernel);
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