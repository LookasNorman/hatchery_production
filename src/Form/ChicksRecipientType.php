<?php

namespace App\Form;

use App\Entity\ChicksRecipient;
use App\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChicksRecipientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'name',
                'placeholder' => 'chicks_recipient.form.placeholder.customer',
                'label' => 'chicks_recipient.form.label.customer',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'chicks_recipient.form.label.name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'chicks_recipient.form.label.email',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'chicks_recipient.form.label.phone_number',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChicksRecipient::class,
        ]);
    }
}
