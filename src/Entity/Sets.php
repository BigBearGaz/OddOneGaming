<?php

namespace App\Entity;

use App\Repository\SetsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SetsRepository::class)]
class Sets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $effect = null;

    /**
     * Type de bonus: 2, 4 ou 6 pièces
     */
    #[ORM\Column(nullable: true)]
    private ?int $pieceType = null;

    /**
     * Nom du set de base (sans le suffixe de pièces)
     * Ex: "Bear" au lieu de "Bear (2-Piece)"
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $baseName = null;

    /**
     * @var Collection<int, Heroes>
     */
    #[ORM\ManyToMany(targetEntity: Heroes::class, mappedBy: 'recommendedSets')]
    private Collection $heroes;

    public function __construct()
    {
        $this->heroes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getEffect(): ?string
    {
        return $this->effect;
    }

    public function setEffect(?string $effect): static
    {
        $this->effect = $effect;
        return $this;
    }

    public function getPieceType(): ?int
    {
        return $this->pieceType;
    }

    public function setPieceType(?int $pieceType): static
    {
        $this->pieceType = $pieceType;
        return $this;
    }

    public function getBaseName(): ?string
    {
        return $this->baseName;
    }

    public function setBaseName(?string $baseName): static
    {
        $this->baseName = $baseName;
        return $this;
    }

    /**
     * @return Collection<int, Heroes>
     */
    public function getHeroes(): Collection
    {
        return $this->heroes;
    }

    public function addHero(Heroes $hero): static
    {
        if (!$this->heroes->contains($hero)) {
            $this->heroes->add($hero);
            $hero->addRecommendedSet($this);
        }
        return $this;
    }

    public function removeHero(Heroes $hero): static
    {
        if ($this->heroes->removeElement($hero)) {
            $hero->removeRecommendedSet($this);
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
