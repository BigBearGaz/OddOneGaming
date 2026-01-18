<?php

namespace App\EventSubscriber;

use App\Repository\DungeonsRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class GlobalDungeonsSubscriber implements EventSubscriberInterface
{
    private DungeonsRepository $dungeonsRepository;
    private Environment $twig;

    public function __construct(DungeonsRepository $dungeonsRepository, Environment $twig)
    {
        $this->dungeonsRepository = $dungeonsRepository;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        // Récupère tous les dungeons
        $dungeons = $this->dungeonsRepository->findAll();

        // Injecte les dungeons comme variable globale Twig
        $this->twig->addGlobal('global_dungeons', $dungeons);
    }
}
