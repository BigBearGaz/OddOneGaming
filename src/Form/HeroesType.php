<?php

namespace App\Form;

use App\Entity\Heroes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HeroesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', TextType::class, [
                'label' => 'Hero Name',
            ])
            ->add('imageUrl', UrlType::class, [
                'label' => 'Image URL',
                'required' => false,
            ])
            ->add('faction', TextType::class, [
                'label' => 'Faction',
                'required' => false,
            ])
            ->add('type', TextType::class, [
                'label' => 'Type',
                'required' => false,
            ])
            ->add('affinity', TextType::class, [
                'label' => 'Affinity',
                'required' => false,
            ])
            ->add('allegiance', TextType::class, [
                'label' => 'Allegiance',
                'required' => false,
            ])
            ->add('Weapons1', TextType::class, [
                'label' => 'Primary Weapon',
                'required' => false,
            ])
            ->add('Weapons2', TextType::class, [
                'label' => 'Secondary Weapon',
                'required' => false,
            ])
            ->add('base', TextareaType::class, [
                'label' => 'Base Skill',
                'required' => false,
            ])
            ->add('core', TextareaType::class, [
                'label' => 'Core Skill',
                'required' => false,
            ])
            ->add('ultimate', TextareaType::class, [
                'label' => 'Ultimate',
                'required' => false,
            ])
            ->add('passive', TextareaType::class, [
                'label' => 'Passive Skill',
                'required' => false,
            ])
            ->add('imprint', TextareaType::class, [
                'label' => 'General Imprint Bonus',
                'required' => false,
            ])
            ->add('Imprint1', TextType::class, [
                'label' => 'Imprint Level I',
                'required' => false,
            ])
            ->add('Imprint2', TextType::class, [
                'label' => 'Imprint Level II',
                'required' => false,
            ])
            ->add('Imprint3', TextType::class, [
                'label' => 'Imprint Level III',
            ])
            ->add('Leader', TextareaType::class, [
                'label' => 'Leader Skill (Optional)',
                'required' => false,
            ])
            // AJOUT DU CHAMP VIDEO
            ->add('videosUrl', UrlType::class, [
                'label' => 'Video URL (YouTube Embed)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://www.youtube.com/embed/VIDEO_ID'
                ],
                'help' => 'Use the YouTube embed URL format: https://www.youtube.com/embed/VIDEO_ID'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Heroes::class,
        ]);
    }
}