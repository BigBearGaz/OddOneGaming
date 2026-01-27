<?php

namespace App\Entity;

use App\Repository\HeroesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HeroesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Heroes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

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

    /** @var Collection<int, Buffs> */
    #[ORM\ManyToMany(targetEntity: Buffs::class)]
    #[ORM\JoinTable(name: 'heroes_buffs')]
    private Collection $heroBuffs;

    /** @var Collection<int, Debuffs> */
    #[ORM\ManyToMany(targetEntity: Debuffs::class)]
    #[ORM\JoinTable(name: 'heroes_debuffs')]
    private Collection $heroDebuffs;

    /** @var Collection<int, Disable> */
    #[ORM\ManyToMany(targetEntity: Disable::class)]
    #[ORM\JoinTable(name: 'heroes_disable')]
    private Collection $heroDisables;

    /**
     * NOUVELLE RELATION : Identique aux Buffs
     * @var Collection<int, Instants>
     */
    #[ORM\ManyToMany(targetEntity: Instants::class)]
    #[ORM\JoinTable(name: 'heroes_instants')]
    private Collection $instants;

    /** @var Collection<int, Sets> */
    #[ORM\ManyToMany(targetEntity: Sets::class, inversedBy: 'heroes')]
    #[ORM\JoinTable(name: 'heroes_sets')]
    private Collection $recommendedSets;

    /** @var Collection<int, Armor> */
    #[ORM\ManyToMany(targetEntity: Armor::class, inversedBy: 'heroes')]
    #[ORM\JoinTable(name: 'heroes_armor')]
    private Collection $armors;

    /** @var Collection<int, Weapons> */
    #[ORM\ManyToMany(targetEntity: Weapons::class, inversedBy: 'heroes')]
    #[ORM\JoinTable(name: 'heroes_weapons')]
    private Collection $weapons;

    /** @var Collection<int, Imprints> */
    #[ORM\ManyToMany(targetEntity: Imprints::class, inversedBy: 'heroes')]
    #[ORM\JoinTable(name: 'heroes_imprints')]
    private Collection $imprints;

    /** @var Collection<int, Awakening> */
    #[ORM\OneToMany(targetEntity: Awakening::class, mappedBy: 'hero', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $awakenings;

    /** @var Collection<int, SkillUpgrade> */
    #[ORM\OneToMany(targetEntity: SkillUpgrade::class, mappedBy: 'hero', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $skillUpgrades;

    public function __construct()
    {
        $this->heroBuffs = new ArrayCollection();
        $this->heroDebuffs = new ArrayCollection();
        $this->heroDisables = new ArrayCollection();
        $this->instants = new ArrayCollection(); // Initialisation
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
        if (!$this->Name) return;
        $slug = mb_strtolower($this->Name);
        $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);
        $slug = trim($slug, '-');
        $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        $slug = preg_replace('~[^-\w]+~', '', $slug);
        $slug = preg_replace('~-+~', '-', $slug);
        $slug = trim($slug, '-');
        $this->slug = $slug ?: 'hero';
    }

    // --- GETTERS / SETTERS ---

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

    // (Note: J'ai raccourci les autres getters pour la lisibilité, garde les tiens tels quels)
    public function getFactionEntity(): ?Faction
    {
        return $this->factionEntity;
    }
    public function setFactionEntity(?Faction $f): static
    {
        $this->factionEntity = $f;
        return $this;
    }
    public function getTypeEntity(): ?Type
    {
        return $this->typeEntity;
    }
    public function setTypeEntity(?Type $t): static
    {
        $this->typeEntity = $t;
        return $this;
    }
    public function getAllegianceEntity(): ?Allegiance
    {
        return $this->allegianceEntity;
    }
    public function setAllegianceEntity(?Allegiance $a): static
    {
        $this->allegianceEntity = $a;
        return $this;
    }
    public function getAffinityEntity(): ?Affinity
    {
        return $this->affinityEntity;
    }
    public function setAffinityEntity(?Affinity $a): static
    {
        $this->affinityEntity = $a;
        return $this;
    }
    public function getLeaderEntity(): ?Leader
    {
        return $this->leaderEntity;
    }
    public function setLeaderEntity(?Leader $l): static
    {
        $this->leaderEntity = $l;
        return $this;
    }
    public function getRarityEntity(): ?Rarity
    {
        return $this->rarityEntity;
    }
    public function setRarityEntity(?Rarity $r): static
    {
        $this->rarityEntity = $r;
        return $this;
    }
    public function getLeaderValue(): ?string
    {
        return $this->leaderValue;
    }
    public function setLeaderValue(?string $v): static
    {
        $this->leaderValue = $v;
        return $this;
    }
    public function getBase(): ?string
    {
        return $this->base;
    }
    public function setBase(?string $b): static
    {
        $this->base = $b;
        return $this;
    }
    public function getCore(): ?string
    {
        return $this->core;
    }
    public function setCore(?string $c): static
    {
        $this->core = $c;
        return $this;
    }
    public function getUltimate(): ?string
    {
        return $this->ultimate;
    }
    public function setUltimate(?string $u): static
    {
        $this->ultimate = $u;
        return $this;
    }
    public function getPassive(): ?string
    {
        return $this->passive;
    }
    public function setPassive(?string $p): static
    {
        $this->passive = $p;
        return $this;
    }
    public function getImprint(): ?string
    {
        return $this->imprint;
    }
    public function setImprint(?string $i): static
    {
        $this->imprint = $i;
        return $this;
    }
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }
    public function setImageUrl(?string $i): static
    {
        $this->imageUrl = $i;
        return $this;
    }
    public function getVideosUrl(): ?string
    {
        return $this->videosUrl;
    }
    public function setVideosUrl(?string $v): static
    {
        $this->videosUrl = $v;
        return $this;
    }
    public function getAwakeningBonuses(): ?string
    {
        return $this->awakeningBonuses;
    }
    public function setAwakeningBonuses(?string $a): static
    {
        $this->awakeningBonuses = $a;
        return $this;
    }
    public function getAscensionBonuses(): ?string
    {
        return $this->ascensionBonuses;
    }
    public function setAscensionBonuses(?string $a): static
    {
        $this->ascensionBonuses = $a;
        return $this;
    }
    public function getDivinityCost(): ?string
    {
        return $this->DivinityCost;
    }
    public function setDivinityCost(?string $d): static
    {
        $this->DivinityCost = $d;
        return $this;
    }
    public function getInitialDivinity(): ?string
    {
        return $this->InitialDivinity;
    }
    public function setInitialDivinity(?string $i): static
    {
        $this->InitialDivinity = $i;
        return $this;
    }

    // --- COLLECTIONS ---

    public function getHeroBuffs(): Collection
    {
        return $this->heroBuffs;
    }
    public function addHeroBuff(Buffs $b): static
    {
        if (!$this->heroBuffs->contains($b)) $this->heroBuffs->add($b);
        return $this;
    }
    public function removeHeroBuff(Buffs $b): static
    {
        $this->heroBuffs->removeElement($b);
        return $this;
    }

    public function getHeroDebuffs(): Collection
    {
        return $this->heroDebuffs;
    }
    public function addHeroDebuff(Debuffs $d): static
    {
        if (!$this->heroDebuffs->contains($d)) $this->heroDebuffs->add($d);
        return $this;
    }
    public function removeHeroDebuff(Debuffs $d): static
    {
        $this->heroDebuffs->removeElement($d);
        return $this;
    }

    public function getHeroDisables(): Collection
    {
        return $this->heroDisables;
    }
    public function addHeroDisable(Disable $d): static
    {
        if (!$this->heroDisables->contains($d)) $this->heroDisables->add($d);
        return $this;
    }
    public function removeHeroDisable(Disable $d): static
    {
        $this->heroDisables->removeElement($d);
        return $this;
    }

    /** @return Collection<int, Instants> */
    public function getInstants(): Collection
    {
        return $this->instants;
    }
    public function addInstant(Instants $i): static
    {
        if (!$this->instants->contains($i)) $this->instants->add($i);
        return $this;
    }
    public function removeInstant(Instants $i): static
    {
        $this->instants->removeElement($i);
        return $this;
    }

    public function getRecommendedSets(): Collection
    {
        return $this->recommendedSets;
    }
    public function addRecommendedSet(Sets $s): static
    {
        if (!$this->recommendedSets->contains($s)) $this->recommendedSets->add($s);
        return $this;
    }
    public function removeRecommendedSet(Sets $s): static
    {
        $this->recommendedSets->removeElement($s);
        return $this;
    }

    public function getArmors(): Collection
    {
        return $this->armors;
    }
    public function addArmor(Armor $a): static
    {
        if (!$this->armors->contains($a)) $this->armors->add($a);
        return $this;
    }
    public function removeArmor(Armor $a): static
    {
        $this->armors->removeElement($a);
        return $this;
    }

    public function getWeapons(): Collection
    {
        return $this->weapons;
    }
    public function addWeapon(Weapons $w): static
    {
        if (!$this->weapons->contains($w)) $this->weapons->add($w);
        return $this;
    }
    public function removeWeapon(Weapons $w): static
    {
        $this->weapons->removeElement($w);
        return $this;
    }

    public function getImprints(): Collection
    {
        return $this->imprints;
    }
    public function addImprint(Imprints $i): static
    {
        if (!$this->imprints->contains($i)) $this->imprints->add($i);
        return $this;
    }
    public function removeImprint(Imprints $i): static
    {
        $this->imprints->removeElement($i);
        return $this;
    }

    public function getAwakenings(): Collection
    {
        return $this->awakenings;
    }
    public function addAwakening(Awakening $a): static
    {
        if (!$this->awakenings->contains($a)) {
            $this->awakenings->add($a);
            $a->setHero($this);
        }
        return $this;
    }
    public function removeAwakening(Awakening $a): static
    {
        if ($this->awakenings->removeElement($a)) {
            if ($a->getHero() === $this) $a->setHero(null);
        }
        return $this;
    }

    public function getSkillUpgrades(): Collection
    {
        return $this->skillUpgrades;
    }
    public function addSkillUpgrade(SkillUpgrade $s): static
    {
        if (!$this->skillUpgrades->contains($s)) {
            $this->skillUpgrades->add($s);
            $s->setHero($this);
        }
        return $this;
    }
    public function removeSkillUpgrade(SkillUpgrade $s): static
    {
        if ($this->skillUpgrades->removeElement($s)) {
            if ($s->getHero() === $this) $s->setHero(null);
        }
        return $this;
    }

    public function getSkillUpgradeByType(string $type): ?SkillUpgrade
    {
        foreach ($this->skillUpgrades as $skillUpgrade) {
            if ($skillUpgrade->getSkillType() === $type) {
                return $skillUpgrade;
            }
        }
        return null;
    }

    public function __toString(): string
    {
        return $this->Name ?? '';
    }
}
