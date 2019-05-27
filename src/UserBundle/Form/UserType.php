<?php

namespace UserBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\Group;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isUpdating = $options['isUpdating'] ?? false;
        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'First name'
                ]
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Last name'
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email'
                ]
            ])
            ->add('username', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Username'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => !$isUpdating,
                'options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'autocomplete' => 'new-password'
                    ],
                ],
                'first_options' => [
                    'required' => !$isUpdating,
                    'label' => 'Password',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Password'
                    ]
                ],
                'second_options' => [
                    'required' => !$isUpdating,
                    'label' => 'Repeat Password',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Repeat password'
                    ]
                ],
                'invalid_message' => 'Password do not match.',
            ])
            ->add('userGroups', EntityType::class,[
                'class' => Group::class,
                'multiple' => true,
                'expanded' => true,
                'label' => 'User Groups',
                'query_builder' => function(EntityRepository $e){
                    return $e->createQueryBuilder('g')
                        ->where('g.deleted = 0')
                        ;
                }
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
            'isUpdating' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_user';
    }


}
