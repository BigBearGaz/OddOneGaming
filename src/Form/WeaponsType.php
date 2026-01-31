<?php

namespace App\Form;

use App\Entity\Weapons;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class WeaponsType extends AbstractType
{
    private $entityManager;

    // On injecte l'EntityManager pour aller chercher les données
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupération dynamique des factions déjà existantes en BDD
        $existingFactions = $this->entityManager->getRepository(Weapons::class)
            ->createQueryBuilder('w')
            ->select('DISTINCT w.faction')
            ->getQuery()
            ->getResult();

        $factionChoices = [];
        foreach ($existingFactions as $f) {
            if ($f['faction']) {
                $factionChoices[$f['faction']] = $f['faction'];
            }
        }

        $builder
            ->add('name', TextType::class, [
                'label' => 'Weapon Name'
            ])
            ->add('rarity', ChoiceType::class, [
                'label' => 'Rarity Level',
                'choices' => [
                    'Rare' => 'Rare',
                    'Epic' => 'Epic',
                    'Legendary' => 'Legendary',
                ],
                'expanded' => true, // Transforme en boutons radio pour un choix rapide
            ])
           ->add('mainStat', TextType::class, [
    'label' => 'Main Stat',
    'required' => false, // Désactive l'obligation côté formulaire
    'attr' => ['placeholder' => 'Ex: ATK +15% (Optional)']
])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('faction', ChoiceType::class, [
                'label' => 'Faction / Realm',
                'choices' => $factionChoices, // Utilise les factions de la BDD
                'placeholder' => 'Select a faction',
            ])
            ->add('imageUrl', UrlType::class, [
                'label' => 'Image URL'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Weapons::class,
        ]);
    }
}