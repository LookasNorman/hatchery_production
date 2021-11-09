<?php

namespace App\Form;

use App\Entity\Herds;
use App\Entity\InputDelivery;
use App\Entity\Inputs;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputDeliveryProductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eggs', IntegerType::class, [
                'label' => 'input_delivery.form.label.eggs',
                'attr' => [
                    'class' => 'form-control'
                ],
                'mapped' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'input_delivery.form.label.button.new',
                'attr' => [
                    'class' => 'btn btn-success m-2'
                ]
            ])
            ->add('saveBreeder', SubmitType::class, [
                'label' => 'input_delivery.form.label.button.new_breeder',
                'attr' => [
                    'class' => 'btn btn-success m-2'
                ]
            ])
            ->add('saveHerd', SubmitType::class, [
                'label' => 'input_delivery.form.label.button.new_herd',
                'attr' => [
                    'class' => 'btn btn-success m-2'
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InputDelivery::class,
        ]);
    }
}
