<?php

namespace App\Form;

use App\Entity\ChickTemperature;
use App\Entity\Hatchers;
use App\Entity\Inputs;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChickTemperatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'chick_temperature.form.label.date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('temperature', NumberType::class, [
                'scale' => 1,
                'label' => 'chick_temperature.form.label.temperature',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('input', EntityType::class, [
                'class' => Inputs::class,
                'choice_label' => function (Inputs $inputs) {
                    $date = $inputs->getInputDate();
                    $date->modify('21 days');
                    return $inputs->getName() . ' - ' . $date->format('Y-m-d');
                },
                'placeholder' => 'chick_temperature.form.placeholder.input',
                'label' => 'chick_temperature.form.label.input',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('hatcher', EntityType::class, [
                'class' => Hatchers::class,
                'choice_label' => 'name',
                'placeholder' => 'chick_temperature.form.placeholder.hatcher',
                'label' => 'chick_temperature.form.label.hatcher',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'chick_temperature.form.label.button.new',
                'attr' => [
                    'class' => 'btn btn-success m-2'
                ]
            ])
            ->add('saveInput', SubmitType::class, [
                'label' => 'chick_temperature.form.label.button.new_input',
                'attr' => [
                    'class' => 'btn btn-success m-2'
                ]
            ])
            ->add('saveHatcher', SubmitType::class, [
                'label' => 'chick_temperature.form.label.button.new_hatcher',
                'attr' => [
                    'class' => 'btn btn-success m-2'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChickTemperature::class,
        ]);
    }
}
