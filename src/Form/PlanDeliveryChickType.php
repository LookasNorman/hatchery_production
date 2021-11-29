<?php

namespace App\Form;

use App\Entity\Breed;
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
            ->add('breed', EntityType::class, [
                'class' => Breed::class,
                'choice_label' => 'name',
                'label' => 'plan_delivery_chick.form.label.breed',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'p-2'
                ]
            ])
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
                'choice_label' => function (ChicksRecipient $chicksRecipient) {
                    return $chicksRecipient->getCustomer()->getName() . ' - ' . $chicksRecipient->getName();
                },
                'label' => 'plan_delivery_chick.form.label.chick_farm',
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('cr')
                        ->join('cr.customer', 'c')
                        ->orderBy('c.name', 'ASC')
                        ->addOrderBy('cr.name', 'ASC');
                },
                'placeholder' => 'plan_delivery_chick.form.placeholder.chick_farm',
                'attr' => [
                    'class' => 'form-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanDeliveryChick::class,
        ]);
    }
}
