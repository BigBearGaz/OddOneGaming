<?php

namespace App\Entity;

use App\Repository\HeroesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeroesRepository::class)]
class Heroes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $faction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allegiance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $base = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $core = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ultimate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passive = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imprint = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $affinity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Leader = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Weapons1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Weapons2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Imprint1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Imprint2 = null;

    #[ORM\Column(length: 255)]
    private ?string $Imprint3 = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $videosUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $debuffs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buffs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $disable = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getFaction(): ?string
    {
        return $this->faction;
    }

    public function setFaction(?string $faction): static
    {
        $this->faction = $faction;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAllegiance(): ?string
    {
        return $this->allegiance;
    }

    public function setAllegiance(?string $allegiance): static
    {
        $this->allegiance = $allegiance;

        return $this;
    }

    public function getBase(): ?string
    {
        return $this->base;
    }

    public function setBase(?string $base): static
    {
        $this->base = $base;

        return $this;
    }

    public function getCore(): ?string
    {
        return $this->core;
    }

    public function setCore(?string $core): static
    {
        $this->core = $core;

        return $this;
    }

    public function getUltimate(): ?string
    {
        return $this->ultimate;
    }

    public function setUltimate(?string $ultimate): static
    {
        $this->ultimate = $ultimate;

        return $this;
    }

    public function getPassive(): ?string
    {
        return $this->passive;
    }

    public function setPassive(?string $passive): static
    {
        $this->passive = $passive;

        return $this;
    }

    public function getImprint(): ?string
    {
        return $this->imprint;
    }

    public function setImprint(?string $imprint): static
    {
        $this->imprint = $imprint;

        return $this;
    }

    public function getAffinity(): ?string
    {
        return $this->affinity;
    }

    public function setAffinity(?string $affinity): static
    {
        $this->affinity = $affinity;

        return $this;
    }

    public function getLeader(): ?string
    {
        return $this->Leader;
    }

    public function setLeader(?string $Leader): static
    {
        $this->Leader = $Leader;

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

    public function getWeapons1(): ?string
    {
        return $this->Weapons1;
    }

    public function setWeapons1(?string $Weapons1): static
    {
        $this->Weapons1 = $Weapons1;

        return $this;
    }

    public function getWeapons2(): ?string
    {
        return $this->Weapons2;
    }

    public function setWeapons2(?string $Weapons2): static
    {
        $this->Weapons2 = $Weapons2;

        return $this;
    }

    public function getImprint1(): ?string
    {
        return $this->Imprint1;
    }

    public function setImprint1(?string $Imprint1): static
    {
        $this->Imprint1 = $Imprint1;

        return $this;
    }

    public function getImprint2(): ?string
    {
        return $this->Imprint2;
    }

    public function setImprint2(?string $Imprint2): static
    {
        $this->Imprint2 = $Imprint2;

        return $this;
    }

    public function getImprint3(): ?string
    {
        return $this->Imprint3;
    }

    public function setImprint3(string $Imprint3): static
    {
        $this->Imprint3 = $Imprint3;

        return $this;
    }

    public function getVideosUrl(): ?string
    {
        return $this->videosUrl;
    }

    public function setVideosUrl(?string $videosUrl): static
    {
        $this->videosUrl = $videosUrl;

        return $this;
    }

    public function getDebuffs(): ?string
    {
        return $this->debuffs;
    }

    public function setDebuffs(?string $debuffs): static
    {
        $this->debuffs = $debuffs;

        return $this;
    }

    public function getBuffs(): ?string
    {
        return $this->buffs;
    }

    public function setBuffs(?string $buffs): static
    {
        $this->buffs = $buffs;

        return $this;
    }

    public function getDisable(): ?string
    {
        return $this->disable;
    }

    public function setDisable(?string $disable): static
    {
        $this->disable = $disable;

        return $this;
    }
}
