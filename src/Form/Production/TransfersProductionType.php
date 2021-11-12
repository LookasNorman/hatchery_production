<?php

namespace App\Form\Production;

use App\Entity\Hatchers;
use App\Entity\Transfers;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransfersProductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transferDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'eggs_inputs_transfer.form.label.transfer_date',
                'data' => new \DateTime(),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('transfersEgg', IntegerType::class, [
                'help' => 'eggs_inputs_transfer.form.help.transfers_egg',
                'label' => 'eggs_inputs_transfer.form.label.transfers_egg',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('hatchers', EntityType::class, [
                'class' => Hatchers::class,
                'choice_label' => 'name',
                'label' => 'eggs_inputs_transfer.form.label.hatcher',
                'placeholder' => 'eggs_inputs_transfer.form.placeholder.hatcher',
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-check'
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
