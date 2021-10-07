<?php

namespace App\Form;

use App\Entity\ChicksRecipient;
use App\Entity\Inputs;
use App\Entity\InputsFarm;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputsFarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chickNumber', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('eggInput', EntityType::class, [
                'class' => Inputs::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('i')
                        ->orderBy('i.name', 'DESC');
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('chicksFarm', EntityType::class, [
                'class' => ChicksRecipient::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('cr')
                        ->orderBy('cr.name', 'ASC');
                },
                'placeholder' => 'inputs_farm.form.placeholder.chicks_farm',
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InputsFarm::class,
        ]);
    }
}
