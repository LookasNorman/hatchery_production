<?php

namespace App\Form;

use App\Entity\Herds;
use App\Entity\PlanDeliveryEgg;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanDeliveryEggType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eggsNumber', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('deliveryDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'plan_delivery_egg.form.label.delivery_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('herd', EntityType::class, [
                'class' => Herds::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanDeliveryEgg::class,
        ]);
    }
}
