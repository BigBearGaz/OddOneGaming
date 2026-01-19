<?php

namespace App\Form;

use App\Entity\DungeonTeamSuggestion;
use App\Entity\Dungeons;
use App\Entity\Heroes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DungeonTeamSuggestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la team',
                'attr' => ['placeholder' => 'Ex: F2P Friendly, Speed Clear...']
            ])
            ->add('dungeon', EntityType::class, [
                'class' => Dungeons::class,
                'choice_label' => 'name',
                'label' => 'Donjon',
                'attr' => ['class' => 'form-select']
            ])
            ->add('heroes', EntityType::class, [
                'class' => Heroes::class,
                'choice_label' => 'name',
                'label' => false,
                'multiple' => true,
                'expanded' => true,
                'mapped' => false, // ✅ Ne pas mapper automatiquement - géré manuellement
                'attr' => ['class' => 'hero-selection-grid']
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Difficulté',
                'choices' => [
                    'Easy' => 'Easy',
                    'Medium' => 'Medium',
                    'Hard' => 'Hard',
                    'Expert' => 'Expert'
                ],
                'placeholder' => 'Choisir une difficulté',
                'required' => false,
                'attr' => ['class' => 'form-select']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description / Stratégie',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Décrivez la stratégie, les synergies, etc.'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DungeonTeamSuggestion::class,
        ]);
    }
}
