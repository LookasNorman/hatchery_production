<?php


namespace App\Form;

use App\Entity\TransportInputsFarm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportInputsFarmTimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('arrivalTime', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'transport_list.form.label.arrival_hour',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TransportInputsFarm::class,
            'input' => null,
        ]);

    }

}
