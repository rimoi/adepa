<?php

namespace App\Form;

use App\Constant\GenderConstant;
use App\Constant\UserConstant;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('telephone', TelType::class, [
                'label' => "Numéro de téléphone",
                'attr' => [
                    'placeholder' => '0606060606'
                ],
                'constraints' => [
                    new Regex('/0\d{9}/')
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom :',
                'attr' => [
                    'placeholder' => 'Jean'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom :',
                'attr' => [
                    'placeholder' => 'Dupond'
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => GenderConstant::MAP,
                'label' => 'Civilité :',
                'expanded' => true,
                'multiple' => false,
                'attr'=> [
                    'class' => 'd-flex cs-gender-bo'
                ]
            ])
            ->add('adress',TextType::class, [
                'label' => 'Adresse :',
                'required' => false,
                'attr' => [
                    'placeholder' => '23 avenue claude monet'
                ]
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
            ->add('iban', FileType::class, [
                'label' => 'IBAN :',
                'required' => false,
                'mapped' => false
            ])
            ->add('siret', TextType::class, [
                'label' => 'SIRET :',
                'required' => false,
                'attr' => [
                    'placeholder' => '36252187900034'
                ]
            ])
            ->add('tva', TextType::class, [
                'label' => 'TVA :',
                'required' => false,
                'attr' => [
                    'placeholder' => 'numéro TVA'
                ]
            ])
            ->add('cni', FileType::class, [
                'label' => 'Pièce d\'identité :',
                'required' => false,
                'mapped' => false
            ])
            ->add('permisConduite', FileType::class, [
                'label' => 'Permis de conduire :',
                'required' => false,
                'mapped' => false
            ])
            ->add('criminalRecord', FileType::class, [
                'label' => 'Extrait casier judiciaire ( Bulletin n°3 ):',
                'required' => false,
                'mapped' => false
            ])
            ->add('autoentrepriseCertificate', FileType::class, [
                'label' => 'Attestation auto-entrepreneur :',
                'required' => false,
                'mapped' => false
            ])

            // CV
//            ->add('file', FileType::class, [
//                'label' => false,
//                'required' => false,
//                'mapped' => false
//            ])
        ;


        if ($options['show_experience'] ?? false) {
            $builder
                ->add('experiences', CollectionType::class, [
                    'label' => false,
                    'entry_type' => ExperienceType::class,
                    'allow_add' => true,
                    'prototype' => true,
                    // these options are passed to each "email" type
                    'entry_options' => [
                        'attr' => ['class' => 'row'],
                    ],
                    'allow_delete' => true,
                    'delete_empty' => true
                ]);

            $builder
                ->add('qualifications', CollectionType::class, [
                    'label' => false,
                    'entry_type' => QualificationType::class,
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

        if ($options['new_user'] ?? false) {
            $builder
                ->add('email', EmailType::class);
        }
        if ($options['edit_user'] ?? false) {
            $user  = $options['data'];
            $roles = $user->getRoles();
                foreach ($roles as $key => $value) {
                    $roles[$key] = UserConstant::asStringInverse($value);
                }

                $builder
                    ->add('enabled', CheckboxType::class, [
                        'label_attr' => ['class' => 'switch-custom'],
                        'label'    => 'Activer ?',
                        'required' => false
                    ])
                    ->add('roles', ChoiceType::class,  [
                        'choices'  => UserConstant::all(),
                        'mapped'   => false,
                        'multiple' => true,
                        'expanded' => true,
                        'data'     => $roles,
                        'required' => false,
                    ])
                    ->add('plainPassword', PasswordType::class, [
                          'label' => 'Mot de passe',
                          'required' => false,
                          'mapped' => false,
                          'attr' => [
                              'placeholder' => '******'
                          ]
                  ]);
            }
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();

        $form->add('categories', EntityType::class, [
            'class' => Category::class,
            'query_builder' => static function (CategoryRepository $repository) {
                return $repository->createQueryBuilder('t')
                    ->innerJoin('t.parent', 'p')
                    ->where('t.archived = :archived')
                    ->setParameter('archived', false)
                    ->addOrderBy('t.title', 'ASC');
            },
            'group_by' => static function (Category $choice) {
                return $choice->getParent()->getTitle();
            },
            'mapped' => true,
            'label' => 'Vous êtes intéressés par quel type de mission ? ( Vous pouvez en choisir plusieurs )',
            'multiple' => true,
            'attr' => [
                'class' => 'js-select2',
                'style' => "width: 100%",
            ],
        ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'new_user' => false,
            'edit_user' => false,
            'show_experience' => false,
            'show_formation' => false,
        ]);
    }
}
