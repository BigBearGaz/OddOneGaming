<?php

namespace App\Command;

use App\Repository\HeroesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:heroes:regenerate-slugs',
    description: 'Regénère les slugs (sans -id), en garantissant l’unicité.',
)]
final class HeroesRegenerateSlugsCommand extends Command
{
    public function __construct(
        private HeroesRepository $heroesRepository,
        private SluggerInterface $slugger,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $heroes = $this->heroesRepository->findBy([], ['id' => 'ASC']);

        // PASS 1: slug temporaire unique (évite toute collision pendant l’update)
        foreach ($heroes as $hero) {
            $hero->setSlug('__tmp-' . $hero->getId());
        }
        $this->em->flush();
        $this->em->clear();

        // PASS 2: vrais slugs propres + -2/-3 si doublon
        $heroes = $this->heroesRepository->findBy([], ['Name' => 'ASC']);

        $used = [];
        $i = 0;

        foreach ($heroes as $hero) {
            $base = (string) $this->slugger->slug($hero->getName())->lower();
            $slug = $base;
            $n = 1;

            while (isset($used[$slug]) || $this->heroesRepository->slugExists($slug, $hero->getId())) {
                $n++;
                $slug = $base . '-' . $n;
            }

            $hero->setSlug($slug);
            $used[$slug] = true;
            $i++;
        }

        $this->em->flush();
        $this->em->clear();

        $output->writeln(sprintf('OK: %d slugs régénérés.', $i));
        return Command::SUCCESS;
    }
}
