<?php

namespace App\Form;

use App\Entity\Breed;
use App\Entity\Supplier;
use App\Entity\Herds;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HerdsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'herds.form.label.name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('hatchingDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'herds.form.label.hatching_date',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('breeder', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => 'name',
                'query_builder' => function(EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'placeholder' => 'herds.form.placeholder.breeder',
                'label' => 'herds.form.label.breeder',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('breed', EntityType::class, [
                'class' => Breed::class,
                'choice_label' => function(Breed $breed) {
                return $breed->getName() . ' ' . $breed->getType();
                },
                'placeholder' => 'herds.form.placeholder.breed',
                'label' => 'herds.form.label.breed',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Herds::class,
        ]);
    }
}
