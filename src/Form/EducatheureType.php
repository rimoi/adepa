<?php

namespace App\Form;

use App\Constant\Days;
use App\Constant\PublicType;
use App\Constant\UserConstant;
use App\Entity\Category;
use App\Entity\Educatheure;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class EducatheureType extends AbstractType
{
    public function __construct(private Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Poste: (*)',
                'attr' => ['placeholder' => 'Ex: Expert du trouble'],
            ])
            ->add('price', NumberType::class, [
                'grouping' => true,
                'scale' => 0,
                'label' => 'Prix (*)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '150'
                ],
            ])
            ->add('zipCode', IntegerType::class, [
                'label' => 'Code Postal (*)',
                'attr' => ['placeholder' => '75012'],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville (*)',
                'attr' => ['placeholder' => 'Paris'],
            ])
            ->add('minDuration', TextType::class, [
                'label' => 'Durée min: (*)',
                'attr' => ['placeholder' => 'Ex: 02h00', 'class' => 'datepicker-hours'],
            ])
            ->add('maxDuration', TextType::class, [
                'label' => 'Durée max: (*)',
                'attr' => ['placeholder' => 'Ex: 06h00', 'class' => 'datepicker-hours'],
            ])
            ->add('numberParticipant', IntegerType::class, [
                'label' => 'Nombre de participant',
                'required' => false,
                'attr' => ['placeholder' => '3'],
            ])
            ->add('publicType', ChoiceType::class, [
                'label' => 'Type de public :',
                'choices' => PublicType::MAP,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('description', CKEditorType::class, [
                'required' => false,
                'label' => 'Description du service : ',
                'config' => array(
                    'uiColor' => '#ffffff',
                    'editorplaceholder' => 'Description...',
                    'defaultLanguage' => 'fr',
                    //...
                ),
            ])
            ->add('noteBooking', CKEditorType::class, [
                'required' => false,
                'label' => 'Note sur la réservation :',
                'config' => array(
                    'uiColor' => '#ffffff',
                    'editorplaceholder' => 'Ajoutez une note qui sera révélée au client lors de la réservation....',
                    'defaultLanguage' => 'fr',
                    //...
                ),
            ])
            ->add('days', ChoiceType::class, [
                'label' => 'Jours de réservation : ',
                'help' => 'Sélectionnez les jours de la semaine disponibles pour la réservation.',
                'choices' => Days::MAP,
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'form-check-inline'
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du service',
                'required' => false,
                'mapped' => false
            ])
            ->add('published', CheckboxType::class, [
                'required' => false,
                'label' => "Publiée la mission ?",
                'label_attr' => ['class' => 'switch-custom'],
            ])
        ;

        if ($this->security->getUser()->hasRole(UserConstant::ROLE_ADMIN)) {
            $builder->add('users', EntityType::class, [
                'class' => User::class,
                'query_builder' => static function (UserRepository $repository) {
                    return $repository->createQueryBuilder('u')
                        ->andWhere('u.roles LIKE :roles')
                        ->setParameter('roles', '%'.UserConstant::ROLE_FREELANCE.'%')
                        ;
                },
                'label' => 'Affecter ce service à des freelances.',
                'choice_label' => function (User $user) {
                    return sprintf('%s',
                        $user->nickname()
                    );
                },
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'js-select2',
                    'style' => "width: 100%",
                ],
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();

        $form->add('categories', EntityType::class, [
            'class' => Category::class,
            'query_builder' => static function (CategoryRepository $repository) {
                return $repository->createQueryBuilder('t')
                    ->innerJoin('t.parent', 'p')
                    ->where('t.archived = :archived')
                    ->setParameter('archived', false)
                    ->addOrderBy('t.title', 'ASC');
            },
            'group_by' => static function (Category $choice) {
                return $choice->getParent()->getTitle();
            },
            'mapped' => true,
            'label' => 'Choisir les sous catégories (*)',
            'multiple' => true,
            'attr' => [
                'class' => 'js-select2',
                'style' => "width: 100%",
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Educatheure::class,
        ]);
    }
}
