<?php

namespace App\Form;

use App\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceType extends AbstractType
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
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Mission ou Formation...',
                    'class' => 'col-12'
                ],
                'label' => 'Titre'
            ])
            ->add('file', FileType::class, [
                'label' => 'Téléchargez votre diplôme',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'col-12'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
        ]);
    }
}
