<?php

namespace App\Form;

use App\Constant\GenderConstant;
use App\Constant\UserConstant;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('telephone', TelType::class, [
                'label' => "Numéro de téléphone(*)",
                'attr' => [
                    'placeholder' => '0606060606'
                ],
                'constraints' => [
                    new Regex(pattern: '/0\d{9}/', message: 'Numéro de téléphone n\'est pas valide !')
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom(*)',
                'attr' => [
                    'placeholder' => 'Jean'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom(*)',
                'attr' => [
                    'placeholder' => 'Dupond'
                ]
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => GenderConstant::MAP,
                'label' => 'Civilité(*)',
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Les extras freelances' => UserConstant::ROLE_FREELANCE,
                    'Les extras clients' => UserConstant::ROLE_CLIENT,
                ],
                'label' => "S'incrire en tant que(*) :",
                'mapped' => false
            ]
            )
            ->add('email', EmailType::class, [
                'label' => 'E-mail(*)',
                'attr' => [
                    'placeholder' => 'jean.dupond@gmail.com'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],
                'label'    => "J'ai lu, compris et accepté les politiques et conditions du site",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez acceptez les CGU du site.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe', 'attr' => ['placeholder' => '*******']],
                'second_options' => ['label' => 'Confirmer mot de passe', 'attr' => ['placeholder' => '*******']],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
