<?php

namespace App\Form;

use App\Entity\Article;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'placeholder' => 'Titre',
                    'class' => 'form-control'
                ],
                'label' => false
            ])
            ->add('content', CKEditorType::class, [
                'attr' => ['placeholder' => 'Contenu'],
                'config' => array(
                    'uiColor' => '#ffffff',
                    //...
                ),
            ])
            ->add('published', CheckboxType::class, [
                'required' => false,
                'label' => "Décocher cette case pour ne pas publié l'article ?",
                'label_attr' => ['class' => 'switch-custom'],
            ])
            ->add('file', FileType::class, [
                'label' => 'Uploader la fiche de Poste ( non obligatoire )',
                'required' => false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
