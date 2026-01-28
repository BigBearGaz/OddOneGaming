<?php

namespace App\Form;

use App\Entity\Affinity;
use App\Entity\Allegiance;
use App\Entity\Armor;
use App\Entity\Heroes;
use App\Entity\Buffs;
use App\Entity\Debuffs;
use App\Entity\Disable;
use App\Entity\Instants; // ✅ Ajout de l'import
use App\Entity\Faction;
use App\Entity\Imprints;
use App\Entity\Leader;
use App\Entity\Rarity;
use App\Entity\Sets;
use App\Entity\Type;
use App\Entity\Weapons;
use App\Entity\SkillUpgrade;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use App\Form\AwakeningType;
use App\Form\SkillUpgradeType;

class HeroesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ========================================
            // INFORMATIONS GÉNÉRALES
            // ========================================
            ->add('Name', TextType::class, [
                'label' => 'Nom du héros',
                'required' => true,
            ])

            ->add('rarityEntity', EntityType::class, [
                'class' => Rarity::class,
                'choice_label' => 'name',
                'label' => 'Rareté',
                'required' => false,
                'multiple' => false,
                'expanded' => true,
                'placeholder' => false,
            ])

            ->add('factionEntity', EntityType::class, [
                'class' => Faction::class,
                'choice_label' => 'name',
                'label' => 'Faction',
                'required' => false,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('typeEntity', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'name',
                'label' => 'Type',
                'required' => false,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('allegianceEntity', EntityType::class, [
                'class' => Allegiance::class,
                'choice_label' => 'name',
                'label' => 'Allégeance',
                'required' => false,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('affinityEntity', EntityType::class, [
                'class' => Affinity::class,
                'choice_label' => 'name',
                'label' => 'Affinité',
                'required' => false,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('leaderEntity', EntityType::class, [
                'class' => Leader::class,
                'choice_label' => 'name',
                'label' => 'Leader',
                'required' => false,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('leaderValue', TextType::class, [
                'label' => 'Valeur du Leader Skill',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: +30%, +15, etc.'
                ]
            ])

            // ========================================
            // URLs
            // ========================================
            ->add('imageUrl', TextType::class, [
                'label' => 'URL Image',
                'required' => false,
            ])
            ->add('videosUrl', TextType::class, [
                'label' => 'URL Vidéo',
                'required' => false,
            ])

            // ========================================
            // COMPÉTENCES
            // ========================================
            ->add('base', TextareaType::class, [
                'label' => 'Compétence de Base',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('core', TextareaType::class, [
                'label' => 'Compétence Core',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('ultimate', TextareaType::class, [
                'label' => 'Compétence Ultimate',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('passive', TextareaType::class, [
                'label' => 'Compétence Passive',
                'required' => false,
                'attr' => ['rows' => 4],
            ])

            // ========================================
            // BUFFS, DEBUFFS, DISABLE, INSTANTS
            // ========================================
            ->add('heroBuffs', EntityType::class, [
                'class' => Buffs::class,
                'choice_label' => 'name',
                'label' => 'Buffs',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('heroDebuffs', EntityType::class, [
                'class' => Debuffs::class,
                'choice_label' => 'name',
                'label' => 'Debuffs',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('heroDisables', EntityType::class, [
                'class' => Disable::class,
                'choice_label' => 'name',
                'label' => 'Disables',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
                     ->add('instants', EntityType::class, [
    'class' => Instants::class,
    'choice_label' => 'name', // Utilise 'name' car 'label' est vide en BDD
    'multiple' => true,
    'expanded' => true,
    'required' => false,
    'choice_attr' => function($choice) {
        // Cela ajoute la description dans une infobulle au survol
        return ['title' => $choice->getDescription()];
    },
])
            // ========================================
            // SETS RECOMMANDÉS
            // ========================================
            ->add('recommendedSets', EntityType::class, [
                'class' => Sets::class,
                'choice_label' => 'name',
                'label' => 'Sets recommandés',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])

            // ========================================
            // ARMURES RECOMMANDÉES - GROUPÉES PAR SLOT
            // ========================================
            ->add('armors', EntityType::class, [
                'class' => Armor::class,
                'choice_label' => function(?Armor $armor) {
                    return $armor ? $armor->getName() : '';
                },
                'label' => 'Armures',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'group_by' => function(?Armor $armor) {
                    if (!$armor) return 'Autres';
                    $slot = $armor->getSlot();
                    $slotNames = [
                        'helmet' => '🪖 Helmet',
                        'pauldrons' => '⚔️ Pauldrons',
                        'chest' => '🛡️ Chest',
                        'gauntlets' => '🥊 Gauntlets',
                        'legs' => '🦵 Legs',
                        'boots' => '👢 Boots',
                    ];
                    return $slotNames[$slot] ?? ucfirst($slot);
                },
                'choice_attr' => function(?Armor $armor) {
                    return ['data-slot' => $armor ? $armor->getSlot() : ''];
                },
            ])

            // ========================================
            // ARMES RECOMMANDÉES
            // ========================================
            ->add('weapons', EntityType::class, [
                'class' => Weapons::class,
                'choice_label' => function(?Weapons $weapon) {
                    if (!$weapon) return '';
                    $mainStat = $weapon->getMainStat() ? ' (' . strtoupper($weapon->getMainStat()) . ')' : '';
                    return $weapon->getName() . $mainStat;
                },
                'label' => 'Armes',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])

            // ========================================
            // IMPRINT
            // ========================================
            ->add('imprint', TextareaType::class, [
                'label' => 'Imprint (Passive unique)',
                'required' => false,
                'attr' => ['rows' => 3],
                'help' => 'Description de la compétence passive unique du héros'
            ])

            ->add('imprints', EntityType::class, [
                'class' => Imprints::class,
                'choice_label' => function(?Imprints $imprint) {
                    if (!$imprint) return '';
                    $rarity = $imprint->getRarity() ? ' [' . $imprint->getRarity()->getName() . ']' : '';
                    return $imprint->getName() . $rarity;
                },
                'label' => 'Imprints (Bonus)',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'group_by' => function(?Imprints $imprint) {
                    return $imprint->getRarity() ? $imprint->getRarity()->getName() : 'Autres';
                },
            ])

            // ========================================
            // DIVINITY
            // ========================================
            ->add('DivinityCost', TextType::class, [
                'label' => 'Coût Divinity',
                'required' => false,
            ])
            ->add('InitialDivinity', TextType::class, [
                'label' => 'Divinity Initiale',
                'required' => false,
            ])

            // ========================================
            // BONUS
            // ========================================
            ->add('awakeningBonuses', TextareaType::class, [
                'label' => 'Bonus Awakening',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('ascensionBonuses', TextareaType::class, [
                'label' => 'Bonus Ascension',
                'required' => false,
                'attr' => ['rows' => 3],
            ])

            // ========================================
            // COLLECTIONS
            // ========================================
            ->add('awakenings', CollectionType::class, [
                'entry_type' => AwakeningType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Skill Awakenings',
                'required' => false,
            ])

            ->add('skillUpgrades', CollectionType::class, [
                'entry_type' => SkillUpgradeType::class,
                'entry_options' => ['label' => false],
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
                'label' => false,
                'required' => false,
            ])
        ;

        // EVENT LISTENERS
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $hero = $event->getData();
            if ($hero && $hero->getSkillUpgrades()->count() === 0) {
                $skillTypes = ['base', 'core', 'ultimate'];
                foreach ($skillTypes as $type) {
                    $skillUpgrade = new SkillUpgrade();
                    $skillUpgrade->setSkillType($type);
                    $skillUpgrade->setHero($hero);
                    $hero->addSkillUpgrade($skillUpgrade);
                }
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $hero = $event->getData();
            $form = $event->getForm();
            $slots = [];
            foreach ($hero->getArmors() as $armor) {
                $slot = $armor->getSlot();
                if (isset($slots[$slot])) {
                    $form->get('armors')->addError(
                        new FormError("Une seule armure par slot. Doublon : " . $slot)
                    );
                    return;
                }
                $slots[$slot] = true;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Heroes::class,
        ]);
    }
}