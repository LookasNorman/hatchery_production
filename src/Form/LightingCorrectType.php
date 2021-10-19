<?php

namespace App\Form;

use App\Entity\Lighting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LightingCorrectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('correctPercent', PercentType::class, [
                'label' => 'eggs_inputs_lighting.form.label.correct_percent',
                'help' => 'eggs_inputs_lighting.form.help.correct_percent',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

}
