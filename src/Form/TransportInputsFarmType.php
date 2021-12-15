<?php

namespace App\Form;

use App\Entity\InputsFarm;
use App\Entity\TransportInputsFarm;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportInputsFarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $input = $options['input'];
        $builder
            ->add('farm', EntityType::class, [
                'class' => InputsFarm::class,
                'choice_label' => function(InputsFarm $inputsFarm){
                    return $inputsFarm->getChicksFarm()->getCustomer()->getName() . ' - ' . $inputsFarm->getChicksFarm()->getName();
                },
                'query_builder' => function(EntityRepository $entityRepository) use($input){
                    return $entityRepository->createQueryBuilder('if')
                        ->andWhere('if.eggInput = :input')
                        ->setParameters(['input' => $input]);
                },
                'label' => 'transport_list.form.label.farm',
                'placeholder' => 'transport_list.form.placeholder.farm',
                'attr' => [
                    'class' => 'form-select'
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
