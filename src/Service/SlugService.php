<?php

namespace App\Service;

use App\Repository\HeroesRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

final class SlugService
{
    public function __construct(
        private SluggerInterface $slugger,
        private HeroesRepository $heroesRepository,
    ) {}

    public function uniqueHeroSlug(string $name, ?int $ignoreId = null): string
    {
        $base = (string) $this->slugger->slug($name)->lower();
        $slug = $base;

        $i = 1;
        while ($this->heroesRepository->slugExists($slug, $ignoreId)) {
            $i++;
            $slug = $base.'-'.$i; // achilles-2, achilles-3, ...
        }

        return $slug;
    }
}
