<?php

namespace App\Form\Plan;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChooseYearPlan extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', DateType::class, [
                'widget' => 'single_text',
                'label' => 'chooseYearPlan.form.label.year',
                'placeholder' => 'chooseYearPlan.form.placeholder.year',
                'format' => 'yyyy',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-danger m-2'
                ]
            ])
        ;
    }

}
