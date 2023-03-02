<?php

namespace App\Form;

use App\Constant\MissionTypeConstant;
use App\Entity\Mission;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        /**
         * @var Mission $mission
         */
        
        $mission = $options['data'];

        $started = $mission->getStarted() ? $mission->getStarted()->format('d/m/Y H:i') : '';
        $ended = $mission->getEnded() ? $mission->getEnded()->format('d/m/Y H:i') : '';

        $builder
            // https://symfony.com/doc/current/form/bootstrap5.html
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Ex: Poste'],
            ])
            ->add('category', TextType::class, [
                'attr' => ['placeholder' => 'handicapé, mineur, personne agé, ...', 'class' => 'form-control'],
                'label' => 'Personne concerné',
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => MissionTypeConstant::MAP,
                'label' => 'Type de service',
                'attr' => [
                    'class' => 'custom-select'
                ],
                'required' => 'false',
            ])
            ->add('content', CKEditorType::class, [
                'required' => 'false',
                'label' => 'false',
                'config' => array(
                    'uiColor' => '#ffffff',
                    'editorplaceholder' => 'Description mission...',
                    'defaultLanguage' => 'fr',
                    //...
                ),
            ])
            ->add('debut', TextType::class, [
                'label' => 'Début mission',
                'attr' => [
                    'class' => 'datepicker'
                ],
                "data" => $started,
                'mapped' => false
            ])
            ->add('fin', TextType::class, [
                'label' => 'Fin mission',
                'attr' => [
                    'class' => 'datepicker'
                ],
                "data" => $ended,
                'mapped' => false
            ])
            ->add('address', TextType::class, [
                'attr' => ['placeholder' => '11 Rue chemin de fer'],
            ])
            ->add('zipCode', IntegerType::class, [
                'attr' => ['placeholder' => '75015'],
            ])
            ->add('city', TextType::class, [
                'attr' => ['placeholder' => 'Paris'],
            ])
            ->add('file', FileType::class, [
                'label' => 'Uploader Fiche Poste ( non obligatoire )',
                'required' => false,
                'mapped' => false
            ])
            ->add('published', CheckboxType::class, [
                'required' => false,
                'label' => "Publiée la mission ?",
                'label_attr' => ['class' => 'switch-custom'],
            ])
            ->add('emergency', CheckboxType::class, [
                'required' => false,
                'label' => "Urgent ?",
                'label_attr' => ['class' => 'switch-custom'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}
