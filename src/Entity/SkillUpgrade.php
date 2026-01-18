<?php

namespace App\Entity;

use App\Repository\SkillUpgradeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SkillUpgradeRepository::class)]
#[ORM\Table(name: 'skill_upgrade')]
class SkillUpgrade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Heroes::class, inversedBy: 'skillUpgrades')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Heroes $hero = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $skillType = null; // 'base', 'core', 'ultimate', 'passive'

    // ✅ NOUVEAU : Cooldown en nombre de tours
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $cooldown = null;

    // ✅ Les 6 niveaux en colonnes séparées (pas de JSON !)
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $level1 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $level2 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $level3 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $level4 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $level5 = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $level6 = null;

    // ===========================
    // GETTERS ET SETTERS
    // ===========================

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

    // ✅ NOUVEAU : Getter cooldown
    public function getCooldown(): ?int
    {
        return $this->cooldown;
    }

    // ✅ NOUVEAU : Setter cooldown
    public function setCooldown(?int $cooldown): self
    {
        $this->cooldown = $cooldown;
        return $this;
    }

    public function getLevel1(): ?string
    {
        return $this->level1;
    }

    public function setLevel1(?string $level1): self
    {
        $this->level1 = $level1;
        return $this;
    }

    public function getLevel2(): ?string
    {
        return $this->level2;
    }

    public function setLevel2(?string $level2): self
    {
        $this->level2 = $level2;
        return $this;
    }

    public function getLevel3(): ?string
    {
        return $this->level3;
    }

    public function setLevel3(?string $level3): self
    {
        $this->level3 = $level3;
        return $this;
    }

    public function getLevel4(): ?string
    {
        return $this->level4;
    }

    public function setLevel4(?string $level4): self
    {
        $this->level4 = $level4;
        return $this;
    }

    public function getLevel5(): ?string
    {
        return $this->level5;
    }

    public function setLevel5(?string $level5): self
    {
        $this->level5 = $level5;
        return $this;
    }

    public function getLevel6(): ?string
    {
        return $this->level6;
    }

    public function setLevel6(?string $level6): self
    {
        $this->level6 = $level6;
        return $this;
    }
}
