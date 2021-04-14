<?php

namespace App\Form;

use App\Entity\EggsDelivery;
use App\Entity\EggSupplier;
use App\Entity\Herds;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EggsDeliveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deliveryDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('eggsNumber', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('firstLayingDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('lastLayingDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('breeder', EntityType::class, [
                'class' => EggSupplier::class,
                'choice_label' => 'name',
                'placeholder' => 'eggs_delivery.form.placeholder.herd',
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
                    'placeholder' => 'wybierz stado',
                    'choices' => $form->getData()->getHerds(),
                    'choice_label' => 'name',
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
            'data_class' => EggsDelivery::class,
        ]);
    }
}
