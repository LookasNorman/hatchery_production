<?php

namespace App\Form;

use App\Entity\ChicksRecipient;
use App\Entity\PlanInput;
use App\Entity\PlanInputFarm;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanInputFarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chickNumber', IntegerType::class, [
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanInputFarm::class,
        ]);
    }
}
