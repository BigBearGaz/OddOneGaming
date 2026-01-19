<?php

namespace App\Form;

use App\Entity\DungeonPassive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DungeonPassiveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la Passive',
                'attr' => [
                    'placeholder' => 'Ex: Mythic Divinity, Venomous Breath',
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description complète de la passive...',
                    'rows' => 4,
                    'class' => 'form-control'
                ]
            ])
            ->add('passiveOrder', IntegerType::class, [
                'label' => 'Ordre',
                'attr' => [
                    'min' => 1,
                    'class' => 'form-control'
                ],
                'data' => 1
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DungeonPassive::class,
        ]);
    }
}
