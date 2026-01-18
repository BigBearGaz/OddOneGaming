<?php

namespace App\Entity;

use App\Repository\ArmorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArmorRepository::class)]
class Armor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $slot = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mainStat = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $subStat1 = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $subStat2 = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $subStat3 = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $subStat4 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Heroes::class, mappedBy: 'armors')]
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

    public function getSlot(): ?string
    {
        return $this->slot;
    }

    public function setSlot(string $slot): static
    {
        $this->slot = $slot;
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

    public function getMainStat(): ?string
    {
        return $this->mainStat;
    }

    public function setMainStat(?string $mainStat): static
    {
        $this->mainStat = $mainStat;
        return $this;
    }

    public function getSubStat1(): ?string
    {
        return $this->subStat1;
    }

    public function setSubStat1(?string $subStat1): static
    {
        $this->subStat1 = $subStat1;
        return $this;
    }

    public function getSubStat2(): ?string
    {
        return $this->subStat2;
    }

    public function setSubStat2(?string $subStat2): static
    {
        $this->subStat2 = $subStat2;
        return $this;
    }

    public function getSubStat3(): ?string
    {
        return $this->subStat3;
    }

    public function setSubStat3(?string $subStat3): static
    {
        $this->subStat3 = $subStat3;
        return $this;
    }

    public function getSubStat4(): ?string
    {
        return $this->subStat4;
    }

    public function setSubStat4(?string $subStat4): static
    {
        $this->subStat4 = $subStat4;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
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
            $hero->addArmor($this);
        }

        return $this;
    }

    public function removeHero(Heroes $hero): static
    {
        if ($this->heroes->removeElement($hero)) {
            $hero->removeArmor($this);
        }

        return $this;
    }
}
