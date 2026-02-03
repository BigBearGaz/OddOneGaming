<?php

namespace App\Form;

use App\Entity\Sets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du set (ex: Bear (2-Piece))',
            ])
            ->add('baseName', TextType::class, [
                'label' => 'Nom de base (ex: Bear)',
                'required' => false,
                'attr' => ['placeholder' => 'Pour regrouper les bonus 2/4/6 pièces'],
            ])
            ->add('pieceType', ChoiceType::class, [
                'label' => 'Type de bonus',
                'choices' => [
                    '2 pièces' => 2,
                    '4 pièces' => 4,
                    '6 pièces' => 6,
                ],
                'placeholder' => 'Sélectionner',
            ])
            ->add('effect', TextareaType::class, [
                'label' => 'Effet / Description du bonus',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('imageUrl', UrlType::class, [
                'label' => 'URL de l\'image',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sets::class,
        ]);
    }
}
