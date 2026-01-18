<?php

namespace App\Entity;

use App\Repository\DungeonsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DungeonsRepository::class)]
class Dungeons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $difficulty = null;

    // SPELL 1
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spell1Name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $spell1Description = null;

    // SPELL 2
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spell2Name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $spell2Description = null;

    #[ORM\Column(nullable: true)]
    private ?int $spell2Cooldown = null;

    // SPELL 3
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spell3Name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $spell3Description = null;

    #[ORM\Column(nullable: true)]
    private ?int $spell3Cooldown = null;

    // PASSIVES DE BASE
    #[ORM\OneToMany(targetEntity: DungeonPassive::class, mappedBy: 'dungeon', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['passiveOrder' => 'ASC'])]
    private Collection $passives;

    // PHASES
    #[ORM\OneToMany(targetEntity: DungeonPhase::class, mappedBy: 'dungeon', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['orderNum' => 'ASC'])]
    private Collection $phases;

    public function __construct()
    {
        $this->passives = new ArrayCollection();
        $this->phases = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(?string $difficulty): static
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    public function getSpell1Name(): ?string
    {
        return $this->spell1Name;
    }

    public function setSpell1Name(?string $spell1Name): static
    {
        $this->spell1Name = $spell1Name;
        return $this;
    }

    public function getSpell1Description(): ?string
    {
        return $this->spell1Description;
    }

    public function setSpell1Description(?string $spell1Description): static
    {
        $this->spell1Description = $spell1Description;
        return $this;
    }

    public function getSpell2Name(): ?string
    {
        return $this->spell2Name;
    }

    public function setSpell2Name(?string $spell2Name): static
    {
        $this->spell2Name = $spell2Name;
        return $this;
    }

    public function getSpell2Description(): ?string
    {
        return $this->spell2Description;
    }

    public function setSpell2Description(?string $spell2Description): static
    {
        $this->spell2Description = $spell2Description;
        return $this;
    }

    public function getSpell2Cooldown(): ?int
    {
        return $this->spell2Cooldown;
    }

    public function setSpell2Cooldown(?int $spell2Cooldown): static
    {
        $this->spell2Cooldown = $spell2Cooldown;
        return $this;
    }

    public function getSpell3Name(): ?string
    {
        return $this->spell3Name;
    }

    public function setSpell3Name(?string $spell3Name): static
    {
        $this->spell3Name = $spell3Name;
        return $this;
    }

    public function getSpell3Description(): ?string
    {
        return $this->spell3Description;
    }

    public function setSpell3Description(?string $spell3Description): static
    {
        $this->spell3Description = $spell3Description;
        return $this;
    }

    public function getSpell3Cooldown(): ?int
    {
        return $this->spell3Cooldown;
    }

    public function setSpell3Cooldown(?int $spell3Cooldown): static
    {
        $this->spell3Cooldown = $spell3Cooldown;
        return $this;
    }

    public function getPassives(): Collection
    {
        return $this->passives;
    }

    public function addPassive(DungeonPassive $passive): static
    {
        if (!$this->passives->contains($passive)) {
            $this->passives->add($passive);
            $passive->setDungeon($this);
        }
        return $this;
    }

    public function removePassive(DungeonPassive $passive): static
    {
        if ($this->passives->removeElement($passive)) {
            if ($passive->getDungeon() === $this) {
                $passive->setDungeon(null);
            }
        }
        return $this;
    }

    public function getPhases(): Collection
    {
        return $this->phases;
    }

    public function addPhase(DungeonPhase $phase): static
    {
        if (!$this->phases->contains($phase)) {
            $this->phases->add($phase);
            $phase->setDungeon($this);
        }
        return $this;
    }

    public function removePhase(DungeonPhase $phase): static
    {
        if ($this->phases->removeElement($phase)) {
            if ($phase->getDungeon() === $this) {
                $phase->setDungeon(null);
            }
        }
        return $this;
    }
}
