<?php

namespace App\Form;

use App\Entity\EggsInputsDetails;
use App\Entity\EggsInputsLighting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EggsInputsLightingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wasteEggs', IntegerType::class, [
                'label' => 'eggs_inputs_lighting.form.label.waste_eggs',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lightingDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'eggs_inputs_lighting.form.label.lighting_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('eggsInputsDetail', EntityType::class, [
                'class' => EggsInputsDetails::class,
                'choice_label' => 'egg_input.name',
                'label' => 'eggs_inputs_lighting.form.label.eggs_inputs_details',
                'placeholder' => 'eggs_inputs_lighting.form.placeholder.eggs_inputs_details',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EggsInputsLighting::class,
        ]);
    }
}
