<?php

namespace App\Entity;

use App\Repository\SetsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SetsRepository::class)]
class Sets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $two = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $four = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $six = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $helmet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pauldrons = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chest = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gauntlets = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $boots = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTwo(): ?string
    {
        return $this->two;
    }

    public function setTwo(?string $two): static
    {
        $this->two = $two;

        return $this;
    }

    public function getFour(): ?string
    {
        return $this->four;
    }

    public function setFour(?string $four): static
    {
        $this->four = $four;

        return $this;
    }

    public function getSix(): ?string
    {
        return $this->six;
    }

    public function setSix(string $six): static
    {
        $this->six = $six;

        return $this;
    }

    public function getHelmet(): ?string
    {
        return $this->helmet;
    }

    public function setHelmet(?string $helmet): static
    {
        $this->helmet = $helmet;

        return $this;
    }

    public function getPauldrons(): ?string
    {
        return $this->pauldrons;
    }

    public function setPauldrons(?string $pauldrons): static
    {
        $this->pauldrons = $pauldrons;

        return $this;
    }

    public function getChest(): ?string
    {
        return $this->chest;
    }

    public function setChest(?string $chest): static
    {
        $this->chest = $chest;

        return $this;
    }

    public function getGauntlets(): ?string
    {
        return $this->gauntlets;
    }

    public function setGauntlets(?string $gauntlets): static
    {
        $this->gauntlets = $gauntlets;

        return $this;
    }

    public function getLegs(): ?string
    {
        return $this->legs;
    }

    public function setLegs(?string $legs): static
    {
        $this->legs = $legs;

        return $this;
    }

    public function getBoots(): ?string
    {
        return $this->boots;
    }

    public function setBoots(?string $boots): static
    {
        $this->boots = $boots;

        return $this;
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
}
