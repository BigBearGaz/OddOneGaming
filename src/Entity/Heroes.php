<?php

namespace App\Entity;

use App\Repository\HeroesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeroesRepository::class)]
#[ORM\HasLifecycleCallbacks] // nécessaire pour que PrePersist/PreUpdate soit pris en compte [web:232]
class Heroes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    // slug: nullable TEMPORAIREMENT pour pouvoir migrer (UNIQUE accepte plusieurs NULL) [web:251]
    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne(targetEntity: Faction::class, inversedBy: 'heroes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Faction $factionEntity = null;

    #[ORM\ManyToOne(targetEntity: Type::class, inversedBy: 'heroes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Type $typeEntity = null;

    #[ORM\ManyToOne(targetEntity: Allegiance::class, inversedBy: 'heroes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Allegiance $allegianceEntity = null;

    #[ORM\ManyToOne(targetEntity: Affinity::class, inversedBy: 'heroes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Affinity $affinityEntity = null;

    #[ORM\ManyToOne(targetEntity: Leader::class, inversedBy: 'heroes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Leader $leaderEntity = null;

    #[ORM\ManyToOne(targetEntity: Rarity::class, inversedBy: 'heroes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Rarity $rarityEntity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $leaderValue = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $base = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $core = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ultimate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $passive = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $imprint = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $videosUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $awakeningBonuses = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ascensionBonuses = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $DivinityCost = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $InitialDivinity = null;

    /**
     * @var Collection<int, Buffs>
     */
    #[ORM\ManyToMany(targetEntity: Buffs::class)]
    #[ORM\JoinTable(name: 'heroes_buffs')]
    private Collection $heroBuffs;

    /**
     * @var Collection<int, Debuffs>
     */
    #[ORM\ManyToMany(targetEntity: Debuffs::class)]
    #[ORM\JoinTable(name: 'heroes_debuffs')]
    private Collection $heroDebuffs;

    /**
     * @var Collection<int, Disable>
     */
    #[ORM\ManyToMany(targetEntity: Disable::class)]
    #[ORM\JoinTable(name: 'heroes_disable')]
    private Collection $heroDisables;

    /**
     * @var Collection<int, Sets>
     */
    #[ORM\ManyToMany(targetEntity: Sets::class, inversedBy: 'heroes')]
    #[ORM\JoinTable(name: 'heroes_sets')]
    private Collection $recommendedSets;

    /**
     * @var Collection<int, Armor>
     */
    #[ORM\ManyToMany(targetEntity: Armor::class, inversedBy: 'heroes')]
    #[ORM\JoinTable(name: 'heroes_armor')]
    private Collection $armors;

    /**
     * @var Collection<int, Weapons>
     */
    #[ORM\ManyToMany(targetEntity: Weapons::class, inversedBy: 'heroes')]
    #[ORM\JoinTable(name: 'heroes_weapons')]
    private Collection $weapons;

    /**
     * @var Collection<int, Imprints>
     */
    #[ORM\ManyToMany(targetEntity: Imprints::class, inversedBy: 'heroes')]
    #[ORM\JoinTable(name: 'heroes_imprints')]
    private Collection $imprints;

    /**
     * @var Collection<int, Awakening>
     */
    #[ORM\OneToMany(targetEntity: Awakening::class, mappedBy: 'hero', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $awakenings;

    /**
     * @var Collection<int, SkillUpgrade>
     */
    #[ORM\OneToMany(targetEntity: SkillUpgrade::class, mappedBy: 'hero', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $skillUpgrades;

    public function __construct()
    {
        $this->heroBuffs = new ArrayCollection();
        $this->heroDebuffs = new ArrayCollection();
        $this->heroDisables = new ArrayCollection();
        $this->recommendedSets = new ArrayCollection();
        $this->armors = new ArrayCollection();
        $this->weapons = new ArrayCollection();
        $this->imprints = new ArrayCollection();
        $this->awakenings = new ArrayCollection();
        $this->skillUpgrades = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateSlug(): void
    {
        if (!$this->Name) {
            return;
        }

        $slug = mb_strtolower($this->Name);
        $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);
        $slug = trim($slug, '-');
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = trim($slug, '-');

        if (!$slug) {
            $slug = 'hero';
        }

        $this->slug = $slug;
    }

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

        // optionnel mais pratique: met à jour en mémoire tout de suite
        $this->updateSlug();

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getFactionEntity(): ?Faction
    {
        return $this->factionEntity;
    }
    public function setFactionEntity(?Faction $factionEntity): static
    {
        $this->factionEntity = $factionEntity;
        return $this;
    }

    public function getTypeEntity(): ?Type
    {
        return $this->typeEntity;
    }
    public function setTypeEntity(?Type $typeEntity): static
    {
        $this->typeEntity = $typeEntity;
        return $this;
    }

    public function getAllegianceEntity(): ?Allegiance
    {
        return $this->allegianceEntity;
    }
    public function setAllegianceEntity(?Allegiance $allegianceEntity): static
    {
        $this->allegianceEntity = $allegianceEntity;
        return $this;
    }

    public function getAffinityEntity(): ?Affinity
    {
        return $this->affinityEntity;
    }
    public function setAffinityEntity(?Affinity $affinityEntity): static
    {
        $this->affinityEntity = $affinityEntity;
        return $this;
    }

    public function getLeaderEntity(): ?Leader
    {
        return $this->leaderEntity;
    }
    public function setLeaderEntity(?Leader $leaderEntity): static
    {
        $this->leaderEntity = $leaderEntity;
        return $this;
    }

    public function getRarityEntity(): ?Rarity
    {
        return $this->rarityEntity;
    }
    public function setRarityEntity(?Rarity $rarityEntity): static
    {
        $this->rarityEntity = $rarityEntity;
        return $this;
    }

    public function getLeaderValue(): ?string
    {
        return $this->leaderValue;
    }
    public function setLeaderValue(?string $leaderValue): static
    {
        $this->leaderValue = $leaderValue;
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

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }
    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;
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

    public function getAwakeningBonuses(): ?string
    {
        return $this->awakeningBonuses;
    }
    public function setAwakeningBonuses(?string $awakeningBonuses): static
    {
        $this->awakeningBonuses = $awakeningBonuses;
        return $this;
    }

    public function getAscensionBonuses(): ?string
    {
        return $this->ascensionBonuses;
    }
    public function setAscensionBonuses(?string $ascensionBonuses): static
    {
        $this->ascensionBonuses = $ascensionBonuses;
        return $this;
    }

    public function getDivinityCost(): ?string
    {
        return $this->DivinityCost;
    }
    public function setDivinityCost(?string $DivinityCost): static
    {
        $this->DivinityCost = $DivinityCost;
        return $this;
    }

    public function getInitialDivinity(): ?string
    {
        return $this->InitialDivinity;
    }
    public function setInitialDivinity(?string $InitialDivinity): static
    {
        $this->InitialDivinity = $InitialDivinity;
        return $this;
    }

    /** @return Collection<int, Buffs> */
    public function getHeroBuffs(): Collection
    {
        return $this->heroBuffs;
    }
    public function addHeroBuff(Buffs $heroBuff): static
    {
        if (!$this->heroBuffs->contains($heroBuff)) {
            $this->heroBuffs->add($heroBuff);
        }
        return $this;
    }
    public function removeHeroBuff(Buffs $heroBuff): static
    {
        $this->heroBuffs->removeElement($heroBuff);
        return $this;
    }

    /** @return Collection<int, Debuffs> */
    public function getHeroDebuffs(): Collection
    {
        return $this->heroDebuffs;
    }
    public function addHeroDebuff(Debuffs $heroDebuff): static
    {
        if (!$this->heroDebuffs->contains($heroDebuff)) {
            $this->heroDebuffs->add($heroDebuff);
        }
        return $this;
    }
    public function removeHeroDebuff(Debuffs $heroDebuff): static
    {
        $this->heroDebuffs->removeElement($heroDebuff);
        return $this;
    }

    /** @return Collection<int, Disable> */
    public function getHeroDisables(): Collection
    {
        return $this->heroDisables;
    }
    public function addHeroDisable(Disable $heroDisable): static
    {
        if (!$this->heroDisables->contains($heroDisable)) {
            $this->heroDisables->add($heroDisable);
        }
        return $this;
    }
    public function removeHeroDisable(Disable $heroDisable): static
    {
        $this->heroDisables->removeElement($heroDisable);
        return $this;
    }

    /** @return Collection<int, Sets> */
    public function getRecommendedSets(): Collection
    {
        return $this->recommendedSets;
    }
    public function addRecommendedSet(Sets $recommendedSet): static
    {
        if (!$this->recommendedSets->contains($recommendedSet)) {
            $this->recommendedSets->add($recommendedSet);
        }
        return $this;
    }
    public function removeRecommendedSet(Sets $recommendedSet): static
    {
        $this->recommendedSets->removeElement($recommendedSet);
        return $this;
    }

    /** @return Collection<int, Armor> */
    public function getArmors(): Collection
    {
        return $this->armors;
    }
    public function addArmor(Armor $armor): static
    {
        if (!$this->armors->contains($armor)) {
            $this->armors->add($armor);
        }
        return $this;
    }
    public function removeArmor(Armor $armor): static
    {
        $this->armors->removeElement($armor);
        return $this;
    }

    public function getArmorBySlot(string $slot): ?Armor
    {
        foreach ($this->armors as $armor) {
            if ($armor->getSlot() === $slot) return $armor;
        }
        return null;
    }

    /** @return Collection<int, Weapons> */
    public function getWeapons(): Collection
    {
        return $this->weapons;
    }
    public function addWeapon(Weapons $weapon): static
    {
        if (!$this->weapons->contains($weapon)) {
            $this->weapons->add($weapon);
        }
        return $this;
    }
    public function removeWeapon(Weapons $weapon): static
    {
        $this->weapons->removeElement($weapon);
        return $this;
    }

    /** @return Collection<int, Imprints> */
    public function getImprints(): Collection
    {
        return $this->imprints;
    }
    public function addImprint(Imprints $imprint): static
    {
        if (!$this->imprints->contains($imprint)) {
            $this->imprints->add($imprint);
        }
        return $this;
    }
    public function removeImprint(Imprints $imprint): static
    {
        $this->imprints->removeElement($imprint);
        return $this;
    }

    /** @return Collection<int, Awakening> */
    public function getAwakenings(): Collection
    {
        return $this->awakenings;
    }
    public function addAwakening(Awakening $awakening): static
    {
        if (!$this->awakenings->contains($awakening)) {
            $this->awakenings->add($awakening);
            $awakening->setHero($this);
        }
        return $this;
    }
    public function removeAwakening(Awakening $awakening): static
    {
        if ($this->awakenings->removeElement($awakening)) {
            if ($awakening->getHero() === $this) $awakening->setHero(null);
        }
        return $this;
    }

    public function getAwakeningsBySkillType(string $skillType): array
    {
        return $this->awakenings->filter(
            fn(Awakening $a) => $a->getSkillType() === $skillType
        )->toArray();
    }

    /** @return Collection<int, SkillUpgrade> */
    public function getSkillUpgrades(): Collection
    {
        return $this->skillUpgrades;
    }
    public function addSkillUpgrade(SkillUpgrade $skillUpgrade): static
    {
        if (!$this->skillUpgrades->contains($skillUpgrade)) {
            $this->skillUpgrades->add($skillUpgrade);
            $skillUpgrade->setHero($this);
        }
        return $this;
    }
    public function removeSkillUpgrade(SkillUpgrade $skillUpgrade): static
    {
        if ($this->skillUpgrades->removeElement($skillUpgrade)) {
            if ($skillUpgrade->getHero() === $this) $skillUpgrade->setHero(null);
        }
        return $this;
    }

    public function getSkillUpgradeByType(string $skillType): ?SkillUpgrade
    {
        foreach ($this->skillUpgrades as $upgrade) {
            if ($upgrade->getSkillType() === $skillType) return $upgrade;
        }
        return null;
    }

    public function __toString(): string
    {
        return $this->Name ?? '';
    }
}
