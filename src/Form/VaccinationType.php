<?php

namespace App\Form;

use App\Entity\ChicksRecipient;
use App\Entity\Herds;
use App\Entity\Vaccination;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VaccinationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eggsNumber', IntegerType::class, [
                'label' => 'vaccination.form.label.eggs_number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('farm', EntityType::class, [
                'class' => ChicksRecipient::class,
                'choice_label' => 'name',
                'label' => 'vaccination.form.label.farm',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('herd', EntityType::class, [
                'class' => Herds::class,
                'choice_label' => 'name',
                'label' => 'vaccination.form.label.herd',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vaccination::class,
        ]);
    }
}
