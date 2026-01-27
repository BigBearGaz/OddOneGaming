<?php

namespace App\Repository;

use App\Entity\Heroes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Heroes>
 */
class HeroesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Heroes::class);
    }

    /**
     * ✅ Filtre les héros selon les critères (incluant les Instants)
     */
    public function findByFilters(
        ?int $factionId = null,
        ?int $typeId = null,
        ?int $affinityId = null,
        ?int $allegianceId = null,
        ?int $rarityId = null,
        ?bool $isLeader = null,
        ?array $buffsIds = null,
        ?array $debuffsIds = null,
        ?array $disableIds = null,
        ?array $instantIds = null // Ajouté
    ): array {
        $qb = $this->createQueryBuilder('h');

        // Jointures de base pour éviter le N+1 lors de l'affichage
        $qb->leftJoin('h.factionEntity', 'f')->addSelect('f')
           ->leftJoin('h.typeEntity', 't')->addSelect('t')
           ->leftJoin('h.rarityEntity', 'r')->addSelect('r');

        // Filtres ManyToOne
        if ($factionId) {
            $qb->andWhere('h.factionEntity = :faction')->setParameter('faction', $factionId);
        }
        if ($typeId) {
            $qb->andWhere('h.typeEntity = :type')->setParameter('type', $typeId);
        }
        if ($affinityId) {
            $qb->andWhere('h.affinityEntity = :affinity')->setParameter('affinity', $affinityId);
        }
        if ($allegianceId) {
            $qb->andWhere('h.allegianceEntity = :allegiance')->setParameter('allegiance', $allegianceId);
        }
        if ($rarityId) {
            $qb->andWhere('h.rarityEntity = :rarity')->setParameter('rarity', $rarityId);
        }
        if ($isLeader === true) {
            $qb->andWhere('h.leaderEntity IS NOT NULL');
        }

        // Filtres ManyToMany
        if (!empty($buffsIds)) {
            $qb->innerJoin('h.heroBuffs', 'buff')
               ->andWhere('buff.id IN (:buffsIds)')
               ->setParameter('buffsIds', $buffsIds);
        }

        if (!empty($debuffsIds)) {
            $qb->innerJoin('h.heroDebuffs', 'debuff')
               ->andWhere('debuff.id IN (:debuffsIds)')
               ->setParameter('debuffsIds', $debuffsIds);
        }

        if (!empty($disableIds)) {
            $qb->innerJoin('h.heroDisables', 'disable')
               ->andWhere('disable.id IN (:disableIds)')
               ->setParameter('disableIds', $disableIds);
        }

        // --- Filtre pour les Instants ---
        if (!empty($instantIds)) {
            $qb->innerJoin('h.instants', 'instant')
               ->andWhere('instant.id IN (:instantIds)')
               ->setParameter('instantIds', $instantIds);
        }

        return $qb->orderBy('h.Name', 'ASC')
                  ->getQuery()
                  ->getResult();
    }

    /**
     * ✅ Récupère tous les héros avec leurs relations chargées (évite le N+1)
     */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.factionEntity', 'f')
            ->leftJoin('h.typeEntity', 't')
            ->leftJoin('h.allegianceEntity', 'al')
            ->leftJoin('h.affinityEntity', 'af')
            ->leftJoin('h.rarityEntity', 'r')
            ->leftJoin('h.leaderEntity', 'l')
            ->addSelect('f', 't', 'al', 'af', 'r', 'l')
            ->orderBy('h.Name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * ✅ Récupère un héros avec TOUTES ses relations (pour la page show)
     */
    public function findOneWithAllRelations(int $id): ?Heroes
    {
        return $this->createQueryBuilder('h')
            ->leftJoin('h.skillUpgrades', 'su')->addSelect('su')
            ->leftJoin('h.awakenings', 'aw')->addSelect('aw')
            ->leftJoin('h.rarityEntity', 'r')->addSelect('r')
            ->leftJoin('h.factionEntity', 'f')->addSelect('f')
            ->leftJoin('h.typeEntity', 't')->addSelect('t')
            ->leftJoin('h.affinityEntity', 'af')->addSelect('af')
            ->leftJoin('h.allegianceEntity', 'al')->addSelect('al')
            ->leftJoin('h.leaderEntity', 'l')->addSelect('l')
            ->leftJoin('h.weapons', 'w')->addSelect('w')
            ->leftJoin('h.armors', 'a')->addSelect('a')
            ->leftJoin('h.imprints', 'i')->addSelect('i')
            ->leftJoin('h.recommendedSets', 's')->addSelect('s')
            ->leftJoin('h.heroBuffs', 'hb')->addSelect('hb')
            ->leftJoin('h.heroDebuffs', 'hd')->addSelect('hd')
            ->leftJoin('h.heroDisables', 'hdi')->addSelect('hdi')
            ->leftJoin('h.instants', 'ins')->addSelect('ins') // Ajouté pour le Show
            ->where('h.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // --- Méthodes de recherche spécifiques ---

    public function findByFaction($factionId): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.factionEntity = :faction')
            ->setParameter('faction', $factionId)
            ->orderBy('h.Name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByType($typeId): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.typeEntity = :type')
            ->setParameter('type', $typeId)
            ->orderBy('h.Name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByRarity($rarityId): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.rarityEntity = :rarity')
            ->setParameter('rarity', $rarityId)
            ->orderBy('h.Name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchByName(string $searchTerm): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.Name LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->orderBy('h.Name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // --- Statistiques / Comptage ---

    public function countByFaction(): array
    {
        return $this->createQueryBuilder('h')
            ->select('IDENTITY(h.factionEntity) as faction_id, COUNT(h.id) as total')
            ->groupBy('h.factionEntity')
            ->getQuery()
            ->getResult();
    }

    public function countByRarity(): array
    {
        return $this->createQueryBuilder('h')
            ->select('IDENTITY(h.rarityEntity) as rarity_id, COUNT(h.id) as total')
            ->groupBy('h.rarityEntity')
            ->getQuery()
            ->getResult();
    }

    // --- Persistence ---

    public function save(Heroes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) { $this->getEntityManager()->flush(); }
    }

    public function remove(Heroes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) { $this->getEntityManager()->flush(); }
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $qb = $this->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->andWhere('h.slug = :slug')
            ->setParameter('slug', $slug);

        if ($ignoreId !== null) {
            $qb->andWhere('h.id != :id')->setParameter('id', $ignoreId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}