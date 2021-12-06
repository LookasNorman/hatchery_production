<?php

namespace App\Form;

use App\Entity\TransportInputsFarm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportInputsFarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('distance', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'transport_list.show.distance'
            ])->add('distanceFromHatchery', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'transport_list.show.distance_hatchery'
            ])
            ->add('arrivalTime', TimeType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'transport_list.form.label.arrival_hour',
                'widget' => 'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TransportInputsFarm::class,
        ]);
    }
}
