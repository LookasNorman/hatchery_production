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
                'label' => 'inputs_farm.form.label.chick_number',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('eggInput', EntityType::class, [
                'label' => 'inputs_farm.form.label.egg_input',
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
                'label' => 'inputs_farm.form.label.chick_farm',
                'class' => ChicksRecipient::class,
                'choice_label' => function (ChicksRecipient $chicksRecipient) {
                    return $chicksRecipient->getCustomer()->getName() . ' - ' . $chicksRecipient->getName();
                },
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('cr')
                        ->join('cr.customer', 'c')
                        ->orderBy('c.name', 'ASC');
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
