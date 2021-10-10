<?php

namespace App\Form;

use App\Entity\ChicksRecipient;
use App\Entity\PlanDeliveryChick;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanDeliveryChickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chickNumber', IntegerType::class, [
                'label' => 'plan_delivery_chick.form.label.chick_number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('deliveryDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'plan_delivery_chick.form.label.delivery_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('chickFarm', EntityType::class, [
                'class' => ChicksRecipient::class,
                'choice_label' => 'name',
                'label' => 'plan_delivery_chick.form.label.chick_farm',
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('cr')
                        ->orderBy('cr.name', 'ASC');
                },
                'placeholder' => 'plan_delivery_chick.placeholder.chick_farm',
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
