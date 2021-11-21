<?php

namespace App\Form\Plan;

use App\Entity\PlanDeliveryChick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanChickDeliveryDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deliveryDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'plan_delivery_chick.form.label.delivery_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanDeliveryChick::class,
        ]);
    }
}
