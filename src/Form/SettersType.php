<?php

namespace App\Form;

use App\Entity\Setters;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'setters.form.label.name',
                'help' => 'setters.form.help.name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('shortname', TextType::class, [
                'label' => 'setters.form.label.shortname',
                'help' => 'setters.form.help.shortname',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Setters::class,
        ]);
    }
}
