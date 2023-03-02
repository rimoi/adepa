<?php

namespace App\Form;

use App\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

class FileType extends AbstractType
{
    // https://symfony.com/doc/6.1/controller/upload_file.html
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // https://stackoverflow.com/questions/53399125/max-size-problem-when-trying-to-upload-a-file-in-symfony
        $builder
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\FileType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
//                'error_bubbling' => true,
                'constraints' => [
                    new FileConstraint([
                        'maxSize' => '3M',
                        'maxSizeMessage' => 'Le fichier est trop volumineux ({{ taille }} {{ suffixe }}). La taille maximale autorisée est de {{ limite }}. {{ suffixe }}'
//                        'mimeTypes' => [
//                            'application/pdf',
//                            'application/x-pdf',
//                        ],
//                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => File::class,
        ]);
    }
}
