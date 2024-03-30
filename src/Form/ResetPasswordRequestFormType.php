<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['autofocus' => 'email', 'placeholder' => 'Email'],
                'label' => false,
                'help' => 'Saisissez votre adresse électronique et nous vous enverrons un lien pour réinitialiser votre mot de passe.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre adresse email',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
