<?php


namespace App\Form;

use App\Entity\EggsDelivery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryPartIndexType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('partIndex', TextType::class, [
                'label' => 'eggs_delivery.form.label.part_index',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EggsDelivery::class,
        ]);
    }
}