<?php

namespace App\Repository;

use App\Entity\Heroes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HeroesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Heroes::class);
    }

    /**
     * Filtre les héros selon les critères
     */
    public function findByFilters(
        ?string $faction = null,
        ?string $type = null,
        ?string $affinity = null,
        ?string $allegiance = null,
        ?string $isLeader = null,
        ?string $buffs = null,
        ?string $debuffs = null,
        ?string $disable = null
    ): array {
        $qb = $this->createQueryBuilder('h');

        if ($faction) {
            $qb->andWhere('h.faction = :faction')
               ->setParameter('faction', $faction);
        }

        if ($type) {
            $qb->andWhere('h.type = :type')
               ->setParameter('type', $type);
        }

        if ($affinity) {
            $qb->andWhere('h.affinity = :affinity')
               ->setParameter('affinity', $affinity);
        }

        if ($allegiance) {
            $qb->andWhere('h.allegiance = :allegiance')
               ->setParameter('allegiance', $allegiance);
        }

        if ($isLeader === 'yes') {
            $qb->andWhere('h.Leader IS NOT NULL')
               ->andWhere('h.Leader != :empty')
               ->setParameter('empty', '');
        }

        // NOUVEAUX FILTRES avec LIKE
        if ($buffs) {
            $qb->andWhere('h.buffs LIKE :buffs')
               ->setParameter('buffs', '%' . $buffs . '%');
        }

        if ($debuffs) {
            $qb->andWhere('h.debuffs LIKE :debuffs')
               ->setParameter('debuffs', '%' . $debuffs . '%');
        }

        if ($disable) {
            $qb->andWhere('h.disable LIKE :disable')
               ->setParameter('disable', '%' . $disable . '%');
        }

        return $qb->orderBy('h.Name', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * Récupère les valeurs distinctes pour un champ simple (faction, type, etc.)
     */
    public function findDistinctValues(string $field): array
    {
        $qb = $this->createQueryBuilder('h')
            ->select("h.$field")
            ->distinct()
            ->where("h.$field IS NOT NULL")
            ->andWhere("h.$field != ''")
            ->orderBy("h.$field", 'ASC');

        $results = $qb->getQuery()->getResult();
        
        return array_filter(
            array_map(fn($row) => $row[$field] ?? null, $results),
            fn($value) => !empty($value)
        );
    }

    /**
     * Récupère les valeurs distinctes pour un champ contenant plusieurs valeurs séparées par virgule
     * (utilisé pour buffs, debuffs, disable)
     */
    public function findDistinctValuesFromCommaSeparated(string $field): array
    {
        // Récupère toutes les valeurs du champ
        $qb = $this->createQueryBuilder('h')
            ->select("h.$field")
            ->where("h.$field IS NOT NULL")
            ->andWhere("h.$field != ''");

        $results = $qb->getQuery()->getResult();

        $uniqueValues = [];

        // Pour chaque ligne, split par virgule et ajoute chaque valeur
        foreach ($results as $row) {
            $value = $row[$field] ?? '';
            if (empty($value)) {
                continue;
            }

            // Split par virgule
            $parts = explode(',', $value);
            
            foreach ($parts as $part) {
                // Nettoie les espaces
                $cleaned = trim($part);
                if (!empty($cleaned)) {
                    $uniqueValues[$cleaned] = true;
                }
            }
        }

        // Récupère les clés (valeurs uniques) et trie
        $result = array_keys($uniqueValues);
        sort($result);

        return $result;
    }

    public function save(Heroes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Heroes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
