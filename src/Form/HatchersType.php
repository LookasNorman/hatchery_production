<?php

namespace App\Form;

use App\Entity\Hatchers;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HatchersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'hatchers.form.label.name',
                'help' => 'hatchers.form.help.name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('shortname', TextType::class, [
                'label' => 'hatchers.form.label.shortname',
                'help' => 'hatchers.form.help.shortname',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hatchers::class,
        ]);
    }
}
