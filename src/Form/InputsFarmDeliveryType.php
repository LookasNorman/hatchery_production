<?php

namespace App\Form;

use App\Entity\Delivery;
use App\Entity\Herds;
use App\Entity\InputsFarm;
use App\Entity\InputsFarmDelivery;
use App\Entity\Supplier;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputsFarmDeliveryType extends AbstractType
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
                            $inputDeliveries = $delivery->getInputsFarmDeliveries();
                            $eggsInput = 0;
                            foreach ($inputDeliveries as $inputDelivery) {
                                $eggsInput = $eggsInput + $inputDelivery->getEggsNumber();
                            }
                            $eggs = $eggs + $delivery->getEggsNumber() - $eggsInput;
                        }
                    }
                    return $eggSupplier->getName() . ' - stan jaj: ' . $eggs;
                },
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('es')
                        ->orderBy('es.name', 'ASC');
                },
                'label' => 'inputs_farm_delivery.form.label.breeder',
                'placeholder' => 'inputs_farm_delivery.form.placeholder.breeder',
                'mapped' => false,
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
                    'label' => 'inputs_farm_delivery.form.label.herd',
                    'placeholder' => 'inputs_farm_delivery.form.placeholder.herd',
                    'choices' => $form->getData()->getHerds(),
                    'choice_label' => function (Herds $herds) {
                        $eggs = 0;
                        $deliveries = $herds->getEggsDeliveries();
                        foreach ($deliveries as $delivery) {
                            $inputDeliveries = $delivery->getInputsFarmDeliveries();
                            $eggsInput = 0;
                            foreach ($inputDeliveries as $inputDelivery) {
                                $eggsInput = $eggsInput + $inputDelivery->getEggsNumber();
                            }
                            $eggs = $eggs + $delivery->getEggsNumber() - $eggsInput;
                        }
                        return $herds->getName() . ' - stan jaj: ' . $eggs;
                    },
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);

                $form->getParent()->add('eggsNumber', IntegerType::class, [
                    'label' => 'inputs_farm_delivery.form.label.eggs_number',
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
            'data_class' => InputsFarmDelivery::class,
        ]);
    }
}
