<?php

namespace App\Form;

use App\Entity\ChicksRecipient;
use App\Entity\EggsDelivery;
use App\Entity\EggsInputs;
use App\Entity\EggsInputsDetails;
use App\Entity\EggSupplier;
use App\Entity\Herds;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EggsInputsDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eggInput', EntityType::class, [
                'class' => EggsInputs::class,
                'choice_label' => 'name',
                'placeholder' => "eggs_inputs_details.form.egg_input",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('breeder', EntityType::class, [
                'class' => EggSupplier::class,
                'choice_label' => function (EggSupplier $eggSupplier) {
                    return $eggSupplier->getName();
                },
                'placeholder' => 'wybierz hodowcę',
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('eggsNumber', IntegerType::class, [
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
                    'placeholder' => 'wybierz stado',
                    'choices' => $form->getData()->getHerds(),
                    'choice_label' => 'name',
                    'required' => true,
                    'mapped' => false,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);

                $form->getParent()->add('chicksRecipient', EntityType::class, [
                    'class' => ChicksRecipient::class,
                    'placeholder' => 'wybierz odbiorcę piskląt',
                    'choice_label' => 'name',
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);

                $form->getParent()->add('chickNumber', IntegerType::class, [
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
            'data_class' => EggsInputsDetails::class,
        ]);
    }
}
