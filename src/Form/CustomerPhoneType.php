<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\CustomerPhone;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerPhoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', TextType::class, [
                'label' => 'customer_phone.form.label.phone_number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('person', TextType::class, [
                'label' => 'customer_phone.form.label.person',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('jobPosition', TextType::class, [
                'label' => 'customer_phone.form.label.job_position',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('description', TextType::class, [
                'label' => 'customer_phone.form.label.description',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $entityRepository){
                return $entityRepository->createQueryBuilder('c')
                    ->orderBy('c.name', 'asc');
                },
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'customer_phone.form.label.customer',
                'placeholder' => 'customer_phone.form.placeholder.customer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerPhone::class,
        ]);
    }
}
