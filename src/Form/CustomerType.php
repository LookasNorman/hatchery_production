<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'customer.form.label.name'
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'customer.form.label.email',
                'required' => false
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'customer.form.label.phone_number',
                'required' => false
            ])
            ->add('postCode', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'customer.form.label.post_code',
                'required' => false
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'customer.form.label.city',
                'required' => false
            ])
            ->add('street', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'customer.form.label.street',
                'required' => false
            ])
            ->add('streetNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'customer.form.label.street_number',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
