<?php

namespace App\Form;

use App\Entity\PlanIndicators;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanIndicatorsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fertilization', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('transferHatchability', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('hatchability', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('settersNumber', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('settersCapacity', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('hatchersNumber', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('hatchersCapacity', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanIndicators::class,
        ]);
    }
}
