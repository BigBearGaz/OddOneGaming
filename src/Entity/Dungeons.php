<?php

namespace App\Entity;

use App\Repository\DungeonsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DungeonsRepository::class)]
class Dungeons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    // SPELL 1 (Basic)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spell1Name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $spell1 = null;

    // SPELL 2 (Core)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spell2Name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $spell2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $spell2Cooldown = null;

    // SPELL 3 (Ultimate)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spell3Name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $spell3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $spell3Cooldown = null;

    // PASSIVES (jusqu'à 4)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $passif = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $passif2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $passif3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $passif4 = null;

    // GETTERS & SETTERS
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
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

    // Spell 1
    public function getSpell1Name(): ?string
    {
        return $this->spell1Name;
    }

    public function setSpell1Name(?string $spell1Name): static
    {
        $this->spell1Name = $spell1Name;
        return $this;
    }

    public function getSpell1(): ?string
    {
        return $this->spell1;
    }

    public function setSpell1(?string $spell1): static
    {
        $this->spell1 = $spell1;
        return $this;
    }

    // Spell 2
    public function getSpell2Name(): ?string
    {
        return $this->spell2Name;
    }

    public function setSpell2Name(?string $spell2Name): static
    {
        $this->spell2Name = $spell2Name;
        return $this;
    }

    public function getSpell2(): ?string
    {
        return $this->spell2;
    }

    public function setSpell2(?string $spell2): static
    {
        $this->spell2 = $spell2;
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

    // Spell 3
    public function getSpell3Name(): ?string
    {
        return $this->spell3Name;
    }

    public function setSpell3Name(?string $spell3Name): static
    {
        $this->spell3Name = $spell3Name;
        return $this;
    }

    public function getSpell3(): ?string
    {
        return $this->spell3;
    }

    public function setSpell3(?string $spell3): static
    {
        $this->spell3 = $spell3;
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

    // Passifs
    public function getPassif(): ?string
    {
        return $this->passif;
    }

    public function setPassif(?string $passif): static
    {
        $this->passif = $passif;
        return $this;
    }

    public function getPassif2(): ?string
    {
        return $this->passif2;
    }

    public function setPassif2(?string $passif2): static
    {
        $this->passif2 = $passif2;
        return $this;
    }

    public function getPassif3(): ?string
    {
        return $this->passif3;
    }

    public function setPassif3(?string $passif3): static
    {
        $this->passif3 = $passif3;
        return $this;
    }

    public function getPassif4(): ?string
    {
        return $this->passif4;
    }

    public function setPassif4(?string $passif4): static
    {
        $this->passif4 = $passif4;
        return $this;
    }
}
