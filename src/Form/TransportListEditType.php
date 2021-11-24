<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Driver;
use App\Entity\Inputs;
use App\Entity\TransportList;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportListEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('departureHour', TimeType::class,[
                'label' => 'transport_list.form.label.departure_hour',
                'attr' => [
                    'class' => 'form-control'
                ],
                'widget' => 'single_text'
            ])
            ->add('driver', EntityType::class, [
                'class' => Driver::class,
                'choice_label' => function(Driver $driver){
                    return $driver->getFirstname() . ' ' . $driver->getLastname();
                },
                'multiple' => true,
                'expanded' => true,
                'label' => 'transport_list.form.label.driver',
                'placeholder' => 'transport_list.form.placeholder.driver',
                'attr' => [
                    'class' => 'form-check'
                ]
            ])
            ->add('car', EntityType::class, [
                'class' => Car::class,
                'choice_label' => function(Car $car){
                    return $car->getName() . ' - ' . $car->getRegistrationNumber();
                },
                'label' => 'transport_list.form.label.car',
                'placeholder' => 'transport_list.form.placeholder.car',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TransportList::class,
        ]);
    }
}
