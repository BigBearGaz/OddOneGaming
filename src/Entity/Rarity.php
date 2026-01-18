<?php

namespace App\Entity;

use App\Repository\RarityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RarityRepository::class)]
class Rarity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Heroes>
     */
    #[ORM\OneToMany(targetEntity: Heroes::class, mappedBy: 'rarityEntity')]
    private Collection $heroes;

    /**
     * @var Collection<int, Imprints>
     */
    #[ORM\OneToMany(targetEntity: Imprints::class, mappedBy: 'rarity')]
    private Collection $imprints;

    public function __construct()
    {
        $this->heroes = new ArrayCollection();
        $this->imprints = new ArrayCollection();
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
            $hero->setRarityEntity($this);
        }
        return $this;
    }

    public function removeHero(Heroes $hero): static
    {
        if ($this->heroes->removeElement($hero)) {
            if ($hero->getRarityEntity() === $this) {
                $hero->setRarityEntity(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Imprints>
     */
    public function getImprints(): Collection
    {
        return $this->imprints;
    }

    public function addImprint(Imprints $imprint): static
    {
        if (!$this->imprints->contains($imprint)) {
            $this->imprints->add($imprint);
            $imprint->setRarity($this);
        }
        return $this;
    }

    public function removeImprint(Imprints $imprint): static
    {
        if ($this->imprints->removeElement($imprint)) {
            if ($imprint->getRarity() === $this) {
                $imprint->setRarity(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
