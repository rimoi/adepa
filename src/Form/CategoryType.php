<?php

namespace App\Form;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Ex: Infirmier'],
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Petite description...',
                    'rows' => 5
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type :',
                'choices' => \App\Constant\CategoryType::MAP,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $form->add('parent', EntityType::class, [
            'class' => Category::class,
            'label' => 'Groupe',
            'choice_label' => 'title',
            'choices' => $this->em->getRepository(Category::class)->getCategoryParent(),
            'required' => false,
            'attr' => [
                'class' => 'js-select2',
                'style' => "width: 100%",
                'placeholder' => 'Veuillez choisir une catégorie...',
            ],
            'empty_data' => $event->getData()->getParent() ?? null,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
