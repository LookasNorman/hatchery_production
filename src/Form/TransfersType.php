<?php

namespace App\Form;

use App\Entity\ChicksRecipient;
use App\Entity\Herds;
use App\Entity\Inputs;
use App\Entity\Transfers;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransfersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transferDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'eggs_inputs_transfer.form.label.transfer_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('input', EntityType::class, [
                'class' => Inputs::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'eggs_inputs_transfer.form.label.eggs_inputs',
                'placeholder' => 'eggs_inputs_transfer.form.placeholder.eggs_inputs'
            ])
            ->add('herd', EntityType::class, [
                'class' => Herds::class,
                'choice_label' => function (Herds $herds) {
                    return $herds->getBreeder()->getName() . ' - ' . $herds->getName();
                },
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'eggs_inputs_transfer.form.label.herd',
                'placeholder' => 'eggs_inputs_transfer.form.placeholder.herd'
            ])
            ->add('farm', EntityType::class, [
                'class' => ChicksRecipient::class,
                'choice_label' => function(ChicksRecipient $chicksRecipient){
                return $chicksRecipient->getCustomer()->getName() . ' - ' . $chicksRecipient->getName();
                },
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'eggs_inputs_transfer.form.label.farm',
                'placeholder' => 'eggs_inputs_transfer.form.placeholder.farm'
            ])
            ->add('transfersEgg', IntegerType::class, [
                'label' => 'eggs_inputs_transfer.form.label.transfers_egg',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transfers::class,
        ]);
    }
}
