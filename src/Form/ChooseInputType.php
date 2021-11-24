<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Inputs;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseInputType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('input', EntityType::class, [
                'class' => Inputs::class,
                'choice_label' => function(Inputs $inputs){
                return $inputs->getName() . ' - ' . $inputs->getSelectionDate()->format('Y-m-d');
                },
                'query_builder' => function(EntityRepository $entityRepository){
                return $entityRepository->createQueryBuilder('i')
                    ->orderBy('i.inputDate', 'DESC');
                },
                'attr' => [
                    'class' => 'form-select'
                ],
                'label' => 'transport_list.form.label.input',
                'placeholder' => 'transport_list.form.placeholder.input'
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
