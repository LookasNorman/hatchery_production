<?php

namespace App\Form;

use App\Entity\Driver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DriverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'driver.form.firstname'
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'driver.form.lastname'
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'driver.form.phone_number'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Driver::class,
        ]);
    }
}
