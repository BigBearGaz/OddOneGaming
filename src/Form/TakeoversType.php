<?php

namespace App\Form;

use App\Entity\Takeovers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TakeoversType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
$builder
    ->add('Name')
    ->add('price')
    ->add('category')  // Ajoute ça
    ->add('details')   // Ajoute ça
;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Takeovers::class,
        ]);
    }
    
}
