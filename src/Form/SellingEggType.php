<?php

namespace App\Form;

use App\Entity\Delivery;
use App\Entity\SellingEgg;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellingEggType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delivery', EntityType::class, [
                'class' => Delivery::class,
                'choice_label' => function (Delivery $delivery) {
                    return $delivery->getHerd()->getBreeder()->getName() . ' - '
                        . $delivery->getHerd()->getName() . ' - '
                        . $delivery->getDeliveryDate()->format('Y-m-d');
                },
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'selling_egg.form.label.delivery',
                'placeholder' => 'selling_egg.form.placeholder.delivery',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'selling_egg.form.label.date',
                'data' => new \DateTime(),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('eggsNumber', IntegerType::class, [
                'label' => 'selling_egg.form.label.eggs_number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SellingEgg::class,
        ]);
    }
}
