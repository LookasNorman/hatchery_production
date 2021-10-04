<?php

namespace App\Form;

use App\Entity\Inputs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'eggs_inputs.form.label.name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('inputDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'eggs_inputs.form.label.input_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Inputs::class,
        ]);
    }
}
