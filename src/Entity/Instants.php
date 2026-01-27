<?php

namespace App\Entity;

use App\Repository\InstantsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstantsRepository::class)]
class Instants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null; // Nom technique

    #[ORM\Column(length: 255)]
    private ?string $label = null; // Nom affiché

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icon = null; // Nom du fichier image

    #[ORM\Column(length: 100)]
    private ?string $category = null; // "buff", "debuff", "godforge"

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

    public function getLabel(): ?string
    {
        return $this->label;
    }
    public function setLabel(string $label): static
    {
        $this->label = $label;
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

    public function getIcon(): ?string
    {
        return $this->icon;
    }
    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }
    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function __toString(): string
    {
        return $this->label ?? '';
    }
}
