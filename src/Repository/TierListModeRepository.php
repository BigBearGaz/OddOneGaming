<?php

namespace App\Repository;

use App\Entity\TierListMode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TierListModeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TierListMode::class);
    }

    /** @return array<string, TierListMode[]> */
    public function findAllGrouped(): array
    {
        $modes = $this->createQueryBuilder('m')
            ->orderBy('m.sortOrder', 'ASC')
            ->getQuery()
            ->getResult();

        $grouped = [];
        foreach ($modes as $mode) {
            $grouped[$mode->getGroupName()][] = $mode;
        }

        return $grouped;
    }
}
