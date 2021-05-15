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

class DeliveryType extends AbstractType
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
            ->add('firstLayingDate', DateType::class, [
                'label' => 'eggs_delivery.form.label.first_laying_date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lastLayingDate', DateType::class, [
                'label' => 'eggs_delivery.form.label.last_laying_date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('breeder', EntityType::class, [
                'label' => 'eggs_delivery.form.label.breeder',
                'class' => Supplier::class,
                'choice_label' => 'name',
                'placeholder' => 'eggs_delivery.form.placeholder.breeder',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);

        $builder->get('breeder')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $form->getParent()->add('herd', EntityType::class, [
                    'class' => Herds::class,
                    'placeholder' => 'eggs_delivery.form.placeholder.herd',
                    'choices' => $form->getData()->getHerds(),
                    'choice_label' => 'name',
                    'label' => 'eggs_delivery.form.label.herd',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Delivery::class,
        ]);
    }
}
