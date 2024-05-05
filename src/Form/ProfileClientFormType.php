<?php

namespace App\Form;

use App\Constant\Days;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileClientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('telephone', TelType::class, [
                'label' => "Numéro de téléphone (*)",
                'attr' => [
                    'placeholder' => '0606060606'
                ],
                'constraints' => [
                    new Regex('/0\d{9}/')
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom : (*)',
                'attr' => [
                    'placeholder' => 'Chef de service'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom : (*)',
                'attr' => [
                    'placeholder' => 'Damien Dupond'
                ]
            ])
            ->add('email', TextType::class, [
                'label' => 'Email :',
                'attr' => [
                    'readonly' => true,
                    'Title' => 'Vous ne pouvez pas modifier votre adresse e-mail. Veuillez contacter votre administrateur si vous souhaitez la changer.'
                ]
            ])
            ->add('adress',TextType::class, [
                'label' => 'Adresse siège social :',
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
//            ->add('minDuration', TextType::class, [
//                'label' => 'De tel heure: (*)',
//                'attr' => ['placeholder' => 'Ex: 02h00', 'class' => 'datepicker-hours'],
//            ])
//            ->add('maxDuration', TextType::class, [
//                'label' => 'A tel heure: (*)',
//                'attr' => ['placeholder' => 'Ex: 06h00', 'class' => 'datepicker-hours'],
//            ])
//            ->add('days', ChoiceType::class, [
//                'label' => 'Jours de disponibilité : ',
//                'help' => 'Sélectionnez les jours de la semaine disponibles pour la réservation.',
//                'choices' => Days::MAP,
//                'expanded' => true,
//                'multiple' => true,
//                'attr' => [
//                    'class' => 'form-check-inline'
//                ]
//            ])

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
