<?php

namespace App\Form;

use App\Entity\Instants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'instant',
                'attr' => ['placeholder' => 'Ex: Explosion Arcanique']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'effet',
                'attr' => ['rows' => 6]
            ])
            ->add('icon', TextType::class, [
                'label' => 'URL de l\'icône',
                'attr' => ['placeholder' => 'https://...']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Instants::class,
        ]);
    }
}