<?php

namespace App\Twig;

use App\Repository\DungeonsRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private DungeonsRepository $dungeonsRepository
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_all_dungeons', [$this, 'getAllDungeons']),
        ];
    }

    public function getAllDungeons(): array
    {
        return $this->dungeonsRepository->findAll();
    }
}
