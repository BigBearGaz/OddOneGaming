<?php

namespace App\Entity;

use App\Repository\AwakeningRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AwakeningRepository::class)]
#[ORM\Table(name: 'awakening')]
class Awakening
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Heroes::class, inversedBy: 'awakenings')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Heroes $hero = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $skillType = null; // 'base', 'core', 'ultimate', 'passive'

    #[ORM\Column]
    private ?int $awakeningLevel = null; // 1, 3, 5, etc.

    #[ORM\Column(type: 'text')]
    private ?string $effectDescription = null;

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHero(): ?Heroes
    {
        return $this->hero;
    }

    public function setHero(?Heroes $hero): self
    {
        $this->hero = $hero;
        return $this;
    }

    public function getSkillType(): ?string
    {
        return $this->skillType;
    }

    public function setSkillType(string $skillType): self
    {
        $this->skillType = $skillType;
        return $this;
    }

    public function getAwakeningLevel(): ?int
    {
        return $this->awakeningLevel;
    }

    public function setAwakeningLevel(int $awakeningLevel): self
    {
        $this->awakeningLevel = $awakeningLevel;
        return $this;
    }

    public function getEffectDescription(): ?string
    {
        return $this->effectDescription;
    }

    public function setEffectDescription(string $effectDescription): self
    {
        $this->effectDescription = $effectDescription;
        return $this;
    }
}
