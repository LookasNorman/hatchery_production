<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\PlanDeliveryEgg;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlanDeliveryEggForHerdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', ChoiceType::class, [
                'choices' => [
                    'plan_delivery_egg_for_herd.form.monday' => 'monday',
                    'plan_delivery_egg_for_herd.form.tuesday' => 'tuesday',
                    'plan_delivery_egg_for_herd.form.wednesday' => 'wednesday',
                    'plan_delivery_egg_for_herd.form.thursday' => 'thursday',
                    'plan_delivery_egg_for_herd.form.friday' => 'friday',
                    'plan_delivery_egg_for_herd.form.saturday' => 'saturday',
                    'plan_delivery_egg_for_herd.form.sunday' => 'sunday'
                    ],
                'mapped' => false,
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'attr' => [
                    'class' => 'form-check'
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanDeliveryEgg::class,
        ]);
    }


}
