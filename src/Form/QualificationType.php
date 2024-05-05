<?php

namespace App\Form;

use App\Entity\Qualification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QualificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('year', TextType::class, [
        'attr' => [
            'placeholder' => '2016-2018',
            'class' => 'col-12'
        ],
        'label' => 'Année'
        ])
        ->add('entreprise', TextType::class, [
            'attr' => [
                'placeholder' => 'Atos, ...',
                'class' => 'col-12'
            ],
            'label' => 'Entreprise'
        ])
        ->add('title', TextType::class, [
            'attr' => [
                'placeholder' => 'Mission ou Formation...',
                'class' => 'col-12'
            ],
            'label' => 'Titre'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Qualification::class,
        ]);
    }
}
