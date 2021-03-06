<?php

namespace App\Form;

use App\Entity\ChicksRecipient;
use App\Entity\Delivery;
use App\Entity\Inputs;
use App\Entity\InputsDetails;
use App\Entity\Supplier;
use App\Entity\Herds;
use App\Repository\DeliveryRepository;
use App\Repository\HerdsRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputsDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('breeder', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => function (Supplier $eggSupplier) {
                    $eggs = 0;
                    $herds = $eggSupplier->getHerds();
                    foreach ($herds as $herd) {
                        $deliveries = $herd->getEggsDeliveries();
                        foreach ($deliveries as $delivery) {
                            $eggs = $eggs + $delivery->getEggsOnWarehouse();
                        }
                    }

                    return $eggSupplier->getName() . ' - stan jaj: ' . $eggs;
                },
                'label' => 'eggs_inputs_details.form.label.breeder',
                'placeholder' => 'eggs_inputs_details.form.placeholder.breeder',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('eggsNumber', IntegerType::class, [
                'mapped' => false,
                'label' => 'eggs_inputs_details.form.label.eggs_number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);

        $builder->get('breeder')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();

                $form->getParent()->add('herd', EntityType::class, [
                    'class' => Herds::class,
                    'label' => 'eggs_inputs_details.form.label.herd',
                    'placeholder' => 'eggs_inputs_details.form.placeholder.herd',
                    'choices' => $form->getData()->getHerds(),
                    'choice_label' => function (Herds $herds) {
                        $eggs = 0;
                        $deliveries = $herds->getEggsDeliveries();
                        foreach ($deliveries as $delivery) {
                            $eggs = $eggs + $delivery->getEggsOnWarehouse();
                        }
                        return $herds->getName() . ' - stan jaj: ' . $eggs;
                    },
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);

                $form->getParent()->add('chicksRecipient', EntityType::class, [
                    'class' => ChicksRecipient::class,
                    'label' => 'eggs_inputs_details.form.label.chicks_recipient',
                    'placeholder' => 'eggs_inputs_details.form.placeholder.chicks_recipient',
                    'choice_label' => 'name',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);

                $form->getParent()->add('chickNumber', IntegerType::class, [
                    'label' => 'eggs_inputs_details.form.label.chick_number',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InputsDetails::class,
        ]);
    }
}
