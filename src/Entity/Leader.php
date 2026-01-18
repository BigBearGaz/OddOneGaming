<?php

namespace App\Entity;

use App\Repository\LeaderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaderRepository::class)]
class Leader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\OneToMany(targetEntity: Heroes::class, mappedBy: 'leaderEntity')]
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
            $hero->setLeaderEntity($this);
        }
        return $this;
    }

    public function removeHero(Heroes $hero): static
    {
        if ($this->heroes->removeElement($hero)) {
            if ($hero->getLeaderEntity() === $this) {
                $hero->setLeaderEntity(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
