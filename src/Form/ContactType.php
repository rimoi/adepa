<?php

namespace App\Form;

use App\Constant\UserConstant;
use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Jean Dupond'
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail(*)',
                'attr' => [
                    'placeholder' => 'jean.dupond@gmail.com'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => "Numéro de téléphone",
                'required' => false,
                'attr' => [
                    'placeholder' => '0606060606'
                ],
                'constraints' => [
                    new Regex('/0\d{9}/')
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Petite description...',
                    'rows' => 5
                ],
            ])
            ->add('type', ChoiceType::class, [
                    'choices' => [
                        'Freelance' => UserConstant::ROLE_FREELANCE,
                        'Client' => UserConstant::ROLE_CLIENT,
                        'Autre' => 'Autre',
                    ],
                    'label' => "Type de client",
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
