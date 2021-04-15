<?php

namespace App\Form;

use App\Entity\EggsDelivery;
use App\Entity\EggsInputs;
use App\Entity\EggsInputsDetails;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EggsInputsDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eggInput', EntityType::class, [
                'class' => EggsInputs::class,
                'choice_label' => 'name',
                'placeholder' => "eggs_inputs_details.form.egg_input",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('chickNumber', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EggsInputsDetails::class,
        ]);
    }
}
