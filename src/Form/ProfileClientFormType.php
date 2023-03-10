<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileClientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('telephone', TelType::class, [
                'label' => "Numéro de téléphone :",
                'attr' => [
                    'placeholder' => '0606060606'
                ],
                'required' => false
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom : (*)',
                'attr' => [
                    'placeholder' => 'Jean'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom : (*)',
                'attr' => [
                    'placeholder' => 'Dupond'
                ]
            ])
            ->add('adress',TextType::class, [
                'label' => 'Adresse :',
                'required' => false,
                'attr' => [
                    'placeholder' => '23 avenue claude monet'
                ],
            ])
            ->add('zipCode',IntegerType::class, [
                'label' => 'Code postal :',
                'required' => false,
                'attr' => [
                    'placeholder' => '75015'
                ]
            ])
            ->add('city',TextType::class, [
                'label' => 'Ville :',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Paris'
                ]
            ])
            ->add('siret', TextType::class, [
                'label' => 'SIRET :',
                'required' => false,
                'attr' => [
                    'placeholder' => '36252187900034'
                ]
            ])
            ->add('socialReason', TextType::class, [
                'label' => 'Raison sociale :',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Raison sociale'
                ]
            ])
            ->add('unityName', TextType::class, [
                'label' => "Nom de l'unité",
                'required' => false,
                'attr' => [
                    'placeholder' => "Nom de l'unité"
                ]
            ])
        ;

        if ($options['show_service'] ?? false) {
            $builder
                ->add('services', CollectionType::class, [
                    'label' => false,
                    'entry_type' => ServiceType::class,
                    'allow_add' => true,
                    'prototype' => true,
                    // these options are passed to each "email" type
                    'entry_options' => [
                        'attr' => ['class' => 'row'],
                    ],
                    'allow_delete' => true,
                    'delete_empty' => true
                ]);

        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'show_service' => false,
        ]);
    }
}
