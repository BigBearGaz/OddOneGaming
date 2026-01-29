<?php

namespace App\Form;

use App\Entity\DungeonPhase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DungeonPhaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la Phase',
                'attr' => ['placeholder' => 'Ex: Exhausted, Enraged', 'class' => 'form-control']
            ])
            ->add('orderNum', IntegerType::class, [
                'label' => 'Ordre',
                'attr' => ['min' => 1, 'class' => 'form-control']
            ])
            ->add('spell1NameOverride', TextType::class, [
                'label' => 'Spell 1 - Nom (Override)',
                'required' => false,
                'attr' => ['placeholder' => 'Laissez vide pour utiliser le spell du donjon', 'class' => 'form-control']
            ])
            ->add('spell1DescriptionOverride', TextareaType::class, [
                'label' => 'Spell 1 - Description (Override)',
                'required' => false,
                'attr' => ['placeholder' => 'Laissez vide pour utiliser le spell du donjon', 'rows' => 3, 'class' => 'form-control']
            ])
            ->add('spell2NameOverride', TextType::class, [
                'label' => 'Spell 2 - Nom (Override)',
                'required' => false,
                'attr' => ['placeholder' => 'Laissez vide pour utiliser le spell du donjon', 'class' => 'form-control']
            ])
            ->add('spell2DescriptionOverride', TextareaType::class, [
                'label' => 'Spell 2 - Description (Override)',
                'required' => false,
                'attr' => ['placeholder' => 'Laissez vide pour utiliser le spell du donjon', 'rows' => 3, 'class' => 'form-control']
            ])
            ->add('spell2CooldownOverride', IntegerType::class, [
                'label' => 'Spell 2 - Cooldown (Override)',
                'required' => false,
                'attr' => ['min' => 0, 'placeholder' => 'Nombre de tours', 'class' => 'form-control']
            ])
            ->add('spell3NameOverride', TextType::class, [
                'label' => 'Spell 3 - Nom (Override)',
                'required' => false,
                'attr' => ['placeholder' => 'Laissez vide pour utiliser le spell du donjon', 'class' => 'form-control']
            ])
            ->add('spell3DescriptionOverride', TextareaType::class, [
                'label' => 'Spell 3 - Description (Override)',
                'required' => false,
                'attr' => ['placeholder' => 'Laissez vide pour utiliser le spell du donjon', 'rows' => 3, 'class' => 'form-control']
            ])
            ->add('spell3CooldownOverride', IntegerType::class, [
                'label' => 'Spell 3 - Cooldown (Override)',
                'required' => false,
                'attr' => ['min' => 0, 'placeholder' => 'Nombre de tours', 'class' => 'form-control']
            ])

            // ✅ IMPORTANT : passives dans le form
->add('passives', CollectionType::class, [
    'entry_type' => DungeonPassiveType::class,
    'mapped' => false,
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
            'data_class' => DungeonPhase::class,
        ]);
    }
}
