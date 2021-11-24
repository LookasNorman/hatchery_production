<?php

namespace App\Form;

use App\Entity\ChickIntegration;
use App\Entity\Inputs;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChickIntegrationChooseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chickIntegration', EntityType::class, [
                'class' => ChickIntegration::class,
                'choice_label' => function(ChickIntegration $chickIntegration){
                    return $chickIntegration->getName();
                },
                'query_builder' => function(EntityRepository $entityRepository){
                    return $entityRepository->createQueryBuilder('ci')
                        ->orderBy('ci.name', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'chick_integration.choose_integration.form.label.chick_integration',
                'placeholder' => 'chick_integration.choose_integration.form.placeholder.chick_integration'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
