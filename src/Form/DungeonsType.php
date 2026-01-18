<?php

namespace App\Form;

use App\Entity\Dungeons;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            // ========== INFOS DUNGEON ==========
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
            
            // ========== SPELL 1 (Basic) ==========
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
            
            // ========== SPELL 2 (Core) ==========
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
            
            // ========== SPELL 3 (Ultimate) ==========
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
            ]);
        
        // Les passives et phases sont gérées en JavaScript
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dungeons::class,
            'allow_extra_fields' => true,  // ✅ AUTORISE LES CHAMPS SUPPLÉMENTAIRES (phases, passives)
        ]);
    }
}
