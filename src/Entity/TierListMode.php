<?php

namespace App\Entity;

use App\Repository\TierListModeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TierListModeRepository::class)]
#[ORM\Table(name: 'tier_list_mode')]
class TierListMode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $slug;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\Column(length: 50, name: 'group_name')]
    private string $groupName;

    #[ORM\Column(length: 255, nullable: true, name: 'image_url')]
    private ?string $imageUrl = null;

    #[ORM\Column(name: 'sort_order')]
    private int $sortOrder = 0;

    public function getId(): ?int { return $this->id; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): static { $this->slug = $slug; return $this; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getGroupName(): string { return $this->groupName; }
    public function setGroupName(string $groupName): static { $this->groupName = $groupName; return $this; }

    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function setImageUrl(?string $imageUrl): static { $this->imageUrl = $imageUrl; return $this; }

    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $sortOrder): static { $this->sortOrder = $sortOrder; return $this; }
}
