<?php

namespace App\Form;

use App\Entity\ContactInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('department', TextType::class, [
                'label' => 'contact_info.form.label.department',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('person', TextType::class, [
                'label' => 'contact_info.form.label.person',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'contact_info.form.label.phone_number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'contact_info.form.label.email',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactInfo::class,
        ]);
    }
}
