<?php

namespace App\Form;

use App\Entity\ChicksRecipient;
use App\Entity\Herds;
use App\Entity\Inputs;
use App\Entity\PlanInput;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanInputType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('input', EntityType::class, [
                'class' => Inputs::class,
                'choice_label' => 'name',
                'placeholder' => 'plan_input.form.placeholder.input',
                'label' => 'plan_input.form.label.input',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('farm', EntityType::class, [
                'class' => ChicksRecipient::class,
                'choice_label' => function (ChicksRecipient $farm) {
                    return $farm->getCustomer()->getName() . ' - ' . $farm->getName();
                },
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('f')
                        ->leftJoin('f.customer', 'c')
                        ->orderBy('c.name', 'ASC')
                        ->addOrderBy('f.name', 'ASC');
                },
                'placeholder' => 'plan_input.form.placeholder.farm',
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'plan_input.form.label.farm'
            ])
            ->add('chickNumber', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'plan_input.form.label.chick_number'
            ])
            ->add('herd', EntityType::class, [
                'class' => Herds::class,
                'choice_label' => function (Herds $herds) {
                    return $herds->getBreeder()->getName() . ' - ' . $herds->getName();
                },
                'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('h')
                    ->leftJoin('h.breeder', 'b')
                    ->andWhere('h.active = true')
                    ->orderBy('b.name', 'ASC')
                    ->addOrderBy('h.name', 'ASC');
                },
                'placeholder' => 'plan_input.form.placeholder.herd',
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'plan_input.form.label.herd'
            ])
            ->add('eggNumber', IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'plan_input.form.label.egg_number'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PlanInput::class,
        ]);
    }
}
