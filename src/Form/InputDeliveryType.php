<?php

namespace App\Form;

use App\Entity\Herds;
use App\Entity\InputDelivery;
use App\Entity\Inputs;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputDeliveryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('input', EntityType::class, [
                'class' => Inputs::class,
                'choice_label' => 'name',
                'label' => 'input_delivery.form.label.input',
                'placeholder' => 'input_delivery.form.placeholder.input',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('eggs', IntegerType::class, [
                'label' => 'input_delivery.form.label.eggs',
                'attr' => [
                    'class' => 'form-control'
                ],
                'mapped' => false
            ])
            ->add('herd', EntityType::class, [
                'class' => Herds::class,
                'choice_label' => function (Herds $herds) {
                    return $herds->getBreeder()->getName() . ' - ' . $herds->getName();
                },
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('h')
                        ->join('h.breeder', 'b')
                        ->andWhere('h.active = true')
                        ->orderBy('b.name', 'asc')
                        ->addOrderBy('h.name', 'asc');
                },
                'label' => 'input_delivery.form.label.herd',
                'placeholder' => 'input_delivery.form.placeholder.herd',
                'attr' => [
                    'class' => 'form-select'
                ],
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InputDelivery::class,
        ]);
    }
}
