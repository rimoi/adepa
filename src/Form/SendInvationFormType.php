<?php

namespace App\Form;

use App\Constant\GenderConstant;
use App\Constant\UserConstant;
use App\Entity\Service;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Regex;

class SendInvitationFormType extends AbstractType
{
    public function __construct(private TokenStorageInterface $token)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** User $user */
        $user =  $this->token->getToken()->getUser();
        //dump($user->getServices()->count());exit;

        $builder
            ->add('telephone', TelType::class, [
                'label' => "Numéro de téléphone(*)",
                'attr' => [
                    'placeholder' => '0605060606'
                ],
                'constraints' => [
                    new Regex(pattern: '/0\d{9}/', message: 'Numéro de téléphone n\'est pas valide, il doit être au format `0605060606` !')
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
                    UserConstant::MANAGER => UserConstant::ROLE_MANAGER,
                    UserConstant::EMPLOYE => UserConstant::ROLE_EMPLOYE,
                ],
                'label' => "Invité en tant que(*) :",
                'mapped' => false
            ]
            )
            ->add('email', EmailType::class, [
                'label' => 'E-mail(*)',
                'attr' => [
                    'placeholder' => 'jean.dupond@gmail.com'
                ]
            ])
            ->add('services', EntityType::class, [
                'choices' =>  $user->getServices(),//[],//$user->getServices(),
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function (Service $service): string {
                    return $service->getUnityName();
                },
                'class' => Service::class,
                'label' => 'Services(*)',
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
