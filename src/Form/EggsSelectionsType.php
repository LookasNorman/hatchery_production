<?php

namespace App\Form;

use App\Entity\EggsSelections;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EggsSelectionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chickNumber')
            ->add('cullChicken')
            ->add('selectionDate')
            ->add('EggsInputsDetail')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EggsSelections::class,
        ]);
    }
}
