<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'dungeon_passive')]
class DungeonPassive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Dungeons::class, inversedBy: 'passives')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Dungeons $dungeon = null;

    #[ORM\ManyToOne(targetEntity: DungeonPhase::class, inversedBy: 'passives')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?DungeonPhase $phase = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private int $passiveOrder = 1;

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

    public function getPhase(): ?DungeonPhase
    {
        return $this->phase;
    }

    public function setPhase(?DungeonPhase $phase): static
    {
        $this->phase = $phase;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPassiveOrder(): int
    {
        return $this->passiveOrder;
    }

    public function setPassiveOrder(int $passiveOrder): static
    {
        $this->passiveOrder = $passiveOrder;
        return $this;
    }
}
