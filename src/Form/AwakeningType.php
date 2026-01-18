<?php

namespace App\Form;

use App\Entity\Awakening;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AwakeningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('skillType', ChoiceType::class, [
                'label' => 'Type de compétence',
                'choices' => [
                    'Base' => 'base',
                    'Core' => 'core',
                    'Ultimate' => 'ultimate',
                    'Passive' => 'passive',
                ],
                'required' => true,
                'placeholder' => 'Sélectionnez un type',
            ])
            ->add('awakeningLevel', IntegerType::class, [
                'label' => 'Niveau d\'awakening',
                'required' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 10,
                    'placeholder' => '1-10',
                ],
            ])
            ->add('effectDescription', TextareaType::class, [
                'label' => 'Description de l\'effet',
                'required' => true,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Décrivez l\'effet de cet awakening...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Awakening::class,
        ]);
    }
}
