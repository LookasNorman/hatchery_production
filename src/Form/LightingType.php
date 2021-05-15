<?php

namespace App\Form;

use App\Entity\Inputs;
use App\Entity\InputsDetails;
use App\Entity\Lighting;
use App\Entity\Supplier;
use App\Entity\Herds;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LightingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('lightingDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'eggs_inputs_lighting.form.label.lighting_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('wasteEggs', IntegerType::class, [
                'label' => 'eggs_inputs_lighting.form.label.waste_eggs',
                'help' => 'eggs_inputs_lighting.form.help.waste_eggs',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lighting::class,
        ]);
    }
}
