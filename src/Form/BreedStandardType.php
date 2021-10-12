<?php

namespace App\Form;

use App\Entity\Breed;
use App\Entity\BreedStandard;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BreedStandardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hatchingEggsWeek', NumberType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('week', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('breed', EntityType::class, [
                'class' => Breed::class,
                'choice_label' => function (Breed $breed) {
                    return $breed->getName() . ' ' . $breed->getType();
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BreedStandard::class,
        ]);
    }
}
