<?php

namespace App\Form;

use App\Entity\Delivery;
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

class DeliveryProductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deliveryDate', DateType::class, [
                'label' => 'eggs_delivery.form.label.delivery_date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('eggsNumber', IntegerType::class, [
                'label' => 'eggs_delivery.form.label.eggs_number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('herd', EntityType::class, [
                'label' => 'eggs_delivery.form.label.breeder',
                'class' => Herds::class,
                'choice_label' => 'name',
                'placeholder' => 'eggs_delivery.form.placeholder.breeder',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Delivery::class,
        ]);
    }
}
