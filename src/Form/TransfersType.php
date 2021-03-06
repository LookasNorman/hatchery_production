<?php

namespace App\Form;

use App\Entity\Inputs;
use App\Entity\InputsDetails;
use App\Entity\Transfers;
use App\Entity\Supplier;
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transfers::class,
        ]);
    }
}
