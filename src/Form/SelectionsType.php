<?php

namespace App\Form;

use App\Entity\Inputs;
use App\Entity\InputsDetails;
use App\Entity\Selections;
use App\Entity\Supplier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chickNumber', IntegerType::class, [
                'label' => 'eggs_inputs_selections.form.label.chick_number',
                'help' => 'eggs_inputs_selections.form.help.chick_number',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('cullChicken', IntegerType::class, [
                'label' => 'eggs_inputs_selections.form.label.cull_chick',
                'help' => 'eggs_inputs_selections.form.help.cull_chick',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('selectionDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'eggs_inputs_selections.form.label.selection_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Selections::class,
        ]);
    }
}
