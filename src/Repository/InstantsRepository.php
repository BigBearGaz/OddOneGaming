<?php

namespace App\Repository;

use App\Entity\Instants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Instants>
 */
class InstantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Instants::class);
    }

    // Exemple de méthode pour tes filtres
    public function findByCategory(string $category): array
    {
        return $this->findBy(['category' => $category]);
    }
}