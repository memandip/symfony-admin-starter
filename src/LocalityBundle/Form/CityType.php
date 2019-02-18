<?php

namespace LocalityBundle\Form;

use Doctrine\ORM\EntityRepository;
use LocalityBundle\Entity\State;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('state', EntityType::class,[
                'class' => State::class,
                'placeholder' => 'Select State',
                'query_builder' => function(EntityRepository $e){
                    return $e->createQueryBuilder('s')
                        ->where('s.deleted = :deleted')
                        ->setParameter('deleted', false);
                }
            ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LocalityBundle\Entity\City'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'localitybundle_city';
    }


}
