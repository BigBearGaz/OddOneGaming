<?php

namespace App\Form;

use App\Entity\Dungeons;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DungeonsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Donjon / Boss',
                'attr' => ['placeholder' => 'Ex: Fafnir', 'class' => 'form-control']
            ])
            ->add('imageUrl', TextType::class, [
                'label' => 'Image',
                'required' => false,
                'attr' => ['placeholder' => 'https://...', 'class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['rows' => 3, 'class' => 'form-control']
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Difficulté',
                'required' => false,
                'choices' => [
                    'Easy' => 'Easy',
                    'Normal' => 'Normal',
                    'Hard' => 'Hard',
                    'Hell' => 'Hell'
                ],
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
                'attr' => ['class' => 'difficulty-radio-group']
            ])

            // spells…
            ->add('spell1Name', TextType::class, [
                'label' => 'Spell 1 - Nom',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Eternal Being', 'class' => 'form-control']
            ])
            ->add('spell1Description', TextareaType::class, [
                'label' => 'Spell 1 - Description',
                'required' => false,
                'attr' => ['rows' => 3, 'class' => 'form-control']
            ])
            ->add('spell2Name', TextType::class, [
                'label' => 'Spell 2 - Nom',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Burning Embers', 'class' => 'form-control']
            ])
            ->add('spell2Description', TextareaType::class, [
                'label' => 'Spell 2 - Description',
                'required' => false,
                'attr' => ['rows' => 3, 'class' => 'form-control']
            ])
            ->add('spell2Cooldown', IntegerType::class, [
                'label' => 'Cooldown',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 0, 'placeholder' => 'Ex: 3']
            ])
            ->add('spell3Name', TextType::class, [
                'label' => 'Spell 3 - Nom',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Fafnir Awakens', 'class' => 'form-control']
            ])
            ->add('spell3Description', TextareaType::class, [
                'label' => 'Spell 3 - Description',
                'required' => false,
                'attr' => ['rows' => 3, 'class' => 'form-control']
            ])
            ->add('spell3Cooldown', IntegerType::class, [
                'label' => 'Cooldown',
                'required' => false,
                'attr' => ['class' => 'form-control', 'min' => 0, 'placeholder' => 'Ex: 0']
            ])

            // ✅ IMPORTANT : phases dans le form
            ->add('phases', CollectionType::class, [
                'entry_type' => DungeonPhaseType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dungeons::class,
            // ⛔ retire allow_extra_fields, sinon tu ne vois pas quand ton JS envoie un truc cassé
            'allow_extra_fields' => false,
        ]);
    }
}
