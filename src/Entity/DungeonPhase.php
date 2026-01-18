<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'dungeon_phase')]
class DungeonPhase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Dungeons::class, inversedBy: 'phases')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Dungeons $dungeon = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private int $orderNum = 1;

    // SPELL 1 OVERRIDE
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spell1NameOverride = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $spell1DescriptionOverride = null;

    // SPELL 2 OVERRIDE
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spell2NameOverride = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $spell2DescriptionOverride = null;

    #[ORM\Column(nullable: true)]
    private ?int $spell2CooldownOverride = null;

    // SPELL 3 OVERRIDE
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spell3NameOverride = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $spell3DescriptionOverride = null;

    #[ORM\Column(nullable: true)]
    private ?int $spell3CooldownOverride = null;

    // PASSIVES DE CETTE PHASE
    #[ORM\OneToMany(targetEntity: DungeonPassive::class, mappedBy: 'phase', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['passiveOrder' => 'ASC'])]
    private Collection $passives;

    public function __construct()
    {
        $this->passives = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDungeon(): ?Dungeons
    {
        return $this->dungeon;
    }

    public function setDungeon(?Dungeons $dungeon): static
    {
        $this->dungeon = $dungeon;
        return $this;
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

    public function getOrderNum(): int
    {
        return $this->orderNum;
    }

    public function setOrderNum(int $orderNum): static
    {
        $this->orderNum = $orderNum;
        return $this;
    }

    public function getSpell1NameOverride(): ?string
    {
        return $this->spell1NameOverride;
    }

    public function setSpell1NameOverride(?string $spell1NameOverride): static
    {
        $this->spell1NameOverride = $spell1NameOverride;
        return $this;
    }

    public function getSpell1DescriptionOverride(): ?string
    {
        return $this->spell1DescriptionOverride;
    }

    public function setSpell1DescriptionOverride(?string $spell1DescriptionOverride): static
    {
        $this->spell1DescriptionOverride = $spell1DescriptionOverride;
        return $this;
    }

    public function getSpell2NameOverride(): ?string
    {
        return $this->spell2NameOverride;
    }

    public function setSpell2NameOverride(?string $spell2NameOverride): static
    {
        $this->spell2NameOverride = $spell2NameOverride;
        return $this;
    }

    public function getSpell2DescriptionOverride(): ?string
    {
        return $this->spell2DescriptionOverride;
    }

    public function setSpell2DescriptionOverride(?string $spell2DescriptionOverride): static
    {
        $this->spell2DescriptionOverride = $spell2DescriptionOverride;
        return $this;
    }

    public function getSpell2CooldownOverride(): ?int
    {
        return $this->spell2CooldownOverride;
    }

    public function setSpell2CooldownOverride(?int $spell2CooldownOverride): static
    {
        $this->spell2CooldownOverride = $spell2CooldownOverride;
        return $this;
    }

    public function getSpell3NameOverride(): ?string
    {
        return $this->spell3NameOverride;
    }

    public function setSpell3NameOverride(?string $spell3NameOverride): static
    {
        $this->spell3NameOverride = $spell3NameOverride;
        return $this;
    }

    public function getSpell3DescriptionOverride(): ?string
    {
        return $this->spell3DescriptionOverride;
    }

    public function setSpell3DescriptionOverride(?string $spell3DescriptionOverride): static
    {
        $this->spell3DescriptionOverride = $spell3DescriptionOverride;
        return $this;
    }

    public function getSpell3CooldownOverride(): ?int
    {
        return $this->spell3CooldownOverride;
    }

    public function setSpell3CooldownOverride(?int $spell3CooldownOverride): static
    {
        $this->spell3CooldownOverride = $spell3CooldownOverride;
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
            $passive->setPhase($this);
        }
        return $this;
    }

    public function removePassive(DungeonPassive $passive): static
    {
        if ($this->passives->removeElement($passive)) {
            if ($passive->getPhase() === $this) {
                $passive->setPhase(null);
            }
        }
        return $this;
    }
}
