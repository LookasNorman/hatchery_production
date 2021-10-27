<?php

namespace App\Form;

use App\Entity\TransportList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('distance')
            ->add('departureHour')
            ->add('arrivalHourToFarm')
            ->add('farm')
            ->add('driver')
            ->add('car')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TransportList::class,
        ]);
    }
}
