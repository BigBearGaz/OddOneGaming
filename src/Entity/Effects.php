<?php

namespace App\Entity;

use App\Repository\EffectsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EffectsRepository::class)]
#[ORM\Table(name: 'effects')]
class Effects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null; // 'buff', 'debuff', 'disable'

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iconUrl = null;

    // ✅ Nouveaux champs pour les icônes par niveau
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iconUrlLevel1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iconUrlLevel2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $iconUrlLevel3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $level1 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $level2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $level3 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    public function setIconUrl(?string $iconUrl): static
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }

    // ✅ Getters/Setters pour iconUrlLevel1
    public function getIconUrlLevel1(): ?string
    {
        return $this->iconUrlLevel1;
    }

    public function setIconUrlLevel1(?string $iconUrlLevel1): static
    {
        $this->iconUrlLevel1 = $iconUrlLevel1;
        return $this;
    }

    // ✅ Getters/Setters pour iconUrlLevel2
    public function getIconUrlLevel2(): ?string
    {
        return $this->iconUrlLevel2;
    }

    public function setIconUrlLevel2(?string $iconUrlLevel2): static
    {
        $this->iconUrlLevel2 = $iconUrlLevel2;
        return $this;
    }

    // ✅ Getters/Setters pour iconUrlLevel3
    public function getIconUrlLevel3(): ?string
    {
        return $this->iconUrlLevel3;
    }

    public function setIconUrlLevel3(?string $iconUrlLevel3): static
    {
        $this->iconUrlLevel3 = $iconUrlLevel3;
        return $this;
    }

    public function getLevel1(): ?string
    {
        return $this->level1;
    }

    public function setLevel1(?string $level1): static
    {
        $this->level1 = $level1;
        return $this;
    }

    public function getLevel2(): ?string
    {
        return $this->level2;
    }

    public function setLevel2(?string $level2): static
    {
        $this->level2 = $level2;
        return $this;
    }

    public function getLevel3(): ?string
    {
        return $this->level3;
    }

    public function setLevel3(?string $level3): static
    {
        $this->level3 = $level3;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Vérifie si l'effet a plusieurs niveaux
     */
    public function hasMultipleLevels(): bool
    {
        return $this->level1 !== null || $this->level2 !== null || $this->level3 !== null;
    }

    /**
     * Retourne le nombre de niveaux disponibles
     */
    public function getLevelCount(): int
    {
        $count = 0;
        if ($this->level1) $count++;
        if ($this->level2) $count++;
        if ($this->level3) $count++;
        return $count;
    }

    /**
     * Retourne un badge de couleur selon le type
     */
    public function getTypeBadgeColor(): string
    {
        return match($this->type) {
            'buff' => 'success',
            'debuff' => 'danger',
            'disable' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Retourne une icône selon le type
     */
    public function getTypeIcon(): string
    {
        return match($this->type) {
            'buff' => '✨',
            'debuff' => '💀',
            'disable' => '🚫',
            default => '⚡'
        };
    }

    /**
     * ✅ Retourne l'icône du niveau approprié ou l'icône par défaut
     */
    public function getIconForLevel(int $level): ?string
    {
        return match($level) {
            1 => $this->iconUrlLevel1 ?? $this->iconUrl,
            2 => $this->iconUrlLevel2 ?? $this->iconUrl,
            3 => $this->iconUrlLevel3 ?? $this->iconUrl,
            default => $this->iconUrl
        };
    }
}
