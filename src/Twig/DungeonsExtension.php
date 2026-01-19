<?php

namespace App\Twig;

use App\Entity\Dungeons;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DungeonsExtension extends AbstractExtension
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_all_dungeons', [$this, 'getAllDungeons']),
        ];
    }

    public function getAllDungeons(): array
    {
        return $this->entityManager->getRepository(Dungeons::class)->findAll();
    }
}
