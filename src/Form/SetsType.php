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
                'label' => 'Set name (e.g. Bear (2-Piece))',
            ])
            ->add('baseName', TextType::class, [
                'label' => 'Base name (e.g. Bear)',
                'required' => false,
                'attr' => ['placeholder' => 'To group 2/4/6-piece bonuses'],
            ])
            ->add('pieceType', ChoiceType::class, [
                'label' => 'Bonus type',
                'choices' => [
                    '2 pieces' => 2,
                    '4 pieces' => 4,
                    '6 pieces' => 6,
                ],
                'placeholder' => 'Select',
            ])
            ->add('effect', TextareaType::class, [
                'label' => 'Effect / Bonus description',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('imageUrl', UrlType::class, [
                'label' => 'Image URL',
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
