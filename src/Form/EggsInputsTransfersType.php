<?php

namespace App\Form;

use App\Entity\EggsInputs;
use App\Entity\EggsInputsDetails;
use App\Entity\EggsInputsTransfers;
use App\Entity\EggSupplier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EggsInputsTransfersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wasteEggs', IntegerType::class, [
                'help' => 'eggs_inputs_transfer.form.help.waste_eggs',
                'label' => 'eggs_inputs_transfer.form.label.waste_eggs',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('transferDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'eggs_inputs_transfer.form.label.transfer_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('eggsInputs', EntityType::class, [
                'class' => EggsInputs::class,
                'choice_label' => 'name',
                'label' => 'eggs_inputs_transfer.form.label.eggs_inputs',
                'mapped' => false,
                'placeholder' => 'eggs_inputs_transfer.form.placeholder.eggs_inputs',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('breeder', EntityType::class, [
                'class' => EggSupplier::class,
                'choice_label' => 'name',
                'label' => 'eggs_inputs_transfer.form.label.breeder',
                'mapped' => false,
                'placeholder' => 'eggs_inputs_transfer.form.placeholder.breeder',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EggsInputsTransfers::class,
        ]);
    }
}
