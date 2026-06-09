<?php

namespace App\Repository;

use App\Entity\HeroTierList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HeroTierList>
 */
class HeroTierListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HeroTierList::class);
    }

    /** Charge toutes les entrées avec le héros en un seul JOIN (évite N+1). */
    public function findAllWithHeroes(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e', 'h')
            ->join('e.hero', 'h')
            ->orderBy('h.Name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
