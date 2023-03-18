<?php

namespace App\Form;

use App\Entity\Service;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'attr' => [
                    'placeholder' => 'Adresse (*) : (Ex : 12 rue chemin de fer)',
                ],
                'label' => false

            ])
            ->add('zipCode', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Code postal (*) : (Ex : 75012)',
                ],
                'label' => false

            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'placeholder' => 'Ville (*) : (Ex : Paris)',
                ],
                'label' => false

            ])
            ->add('phone', TelType::class, [
                'label' => "Numéro de téléphone",
                'attr' => [
                    'placeholder' => 'Tél (*) : (Ex : 0606060606)'
                ],
                'constraints' => [
                    new Regex('/0\d{9}/')
                ]
            ])
            ->add('contactName', TextType::class, [
                'attr' => [
                    'placeholder' => 'Référent a appeler  (*) ( Ex: Jean dupond )',
                ],
                'label' => false
            ])
            ->add('public', TextType::class, [
                'attr' => [
                    'placeholder' => 'Public...',
                ],
                'label' => false,
                'required' => false
            ])
            ->add('unityName', TextType::class, [
                'label' => "Nom de l'unité (*) ",
                'attr' => [
                    'placeholder' => "Nom de l'unité (*) : (Ex: Service logistique)"
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }
}
