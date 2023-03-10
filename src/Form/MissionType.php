<?php

namespace App\Form;

use App\Constant\MissionTypeConstant;
use App\Entity\Category;
use App\Entity\Mission;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

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
                'label' => 'Public',
                'attr' => ['placeholder' => 'Ex: Poste'],
            ])
            ->add('category', ChoiceType::class, [
                'choices' => MissionTypeConstant::MAP,
                'choice_label' => function (?string $category) {
                    return $category ? strtoupper($category) : '';
                },
                'label' => 'Type public',
                'required' => false,
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'custom-select'
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => "Num??ro de t??l??phone",
                'attr' => [
                    'placeholder' => '0606060606'
                ],
                'constraints' => [
                    new Regex('/0\d{9}/')
                ],
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
                'label' => 'D??but mission',
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
                'label' => "Publi??e la mission ?",
                'label_attr' => ['class' => 'switch-custom'],
            ])
            ->add('emergency', CheckboxType::class, [
                'required' => false,
                'label' => "Urgent ?",
                'label_attr' => ['class' => 'switch-custom'],
            ])
        ;

        if ($options['user'] ?? false) {
            $builder->add('users', EntityType::class, [
                'class' => User::class,
                'query_builder' => static function (UserRepository $repository) {
                    return $repository->createQueryBuilder('u')
                        ->where('u.enabled = :enabled')
                        ->setParameter('enabled', true);
                },
                'mapped' => false,
                'label' => 'Envoie une notification a ces personnes ',
                'choice_label' => function (User $user) {
                    return sprintf('%s ( %s )',
                        $user->nickname(),
                        $user->getEmail()
                    );
                },
                'multiple' => true,
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
                    ->addOrderBy('t.title', 'ASC');
            },
            'group_by' => static function (Category $choice) {
                return $choice->getParent()->getTitle();
            },
            'mapped' => true,
            'label' => 'Type de service ( Vous pourriez choisir plusieurs )',
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
            'data_class' => Mission::class,
            'user' => null
        ]);
    }
}
