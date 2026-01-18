<?php

namespace App\Form;

use App\Entity\SkillUpgrade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillUpgradeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // skillType est maintenant caché car pré-rempli
            ->add('skillType', TextType::class, [
                'label' => false,
                'attr' => [
                    'readonly' => true,
                    'style' => 'display:none;',
                ],
            ])
            
            // ✅ NOUVEAU : Cooldown
            ->add('cooldown', IntegerType::class, [
                'label' => 'Cooldown (tours)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nombre de tours',
                    'min' => 0,
                ],
            ]);

        // Ajouter les 6 champs level1 à level6
        for ($i = 1; $i <= 6; $i++) {
            $builder->add('level' . $i, TextType::class, [
                'label' => 'Level ' . $i,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Effet du niveau ' . $i,
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SkillUpgrade::class,
        ]);
    }
}
