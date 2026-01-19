<?php

namespace App\Entity;

use App\Repository\HeroTierListRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeroTierListRepository::class)]
class HeroTierList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $tier = null;

    #[ORM\Column(length: 50)]
    private ?string $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: 'ranking_order')]  // ✅ Ajout du mapping explicite
    private ?int $rankingOrder = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]  // ✅ Ajout du mapping explicite
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Heroes::class)]
    #[ORM\JoinColumn(name: 'hero_id', nullable: false)]  // ✅ Ajout du mapping explicite
    private ?Heroes $hero = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTier(): ?string
    {
        return $this->tier;
    }

    public function setTier(string $tier): static
    {
        $this->tier = $tier;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getRankingOrder(): ?int
    {
        return $this->rankingOrder;
    }

    public function setRankingOrder(int $rankingOrder): static
    {
        $this->rankingOrder = $rankingOrder;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getHero(): ?Heroes
    {
        return $this->hero;
    }

    public function setHero(?Heroes $hero): static
    {
        $this->hero = $hero;
        return $this;
    }
}
