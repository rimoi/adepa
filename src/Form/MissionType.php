<?php

namespace App\Form;

use App\Constant\MissionTypeConstant;
use App\Constant\UserConstant;
use App\Entity\Booking;
use App\Entity\Category;
use App\Entity\Mission;
use App\Entity\Service;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Regex;

class MissionType extends AbstractType
{
    private Security $security;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        /**
         * @var Mission $mission
         */
        
        $mission = $options['data'];

        $user = $this->security->getUser();

        // Liste des utilisateurs auquel elle a déjà travaillé avec
        if ($user->hasRole(UserConstant::ROLE_ADMIN)) {
            $userFreelances = $this->entityManager->getRepository(User::class)->getFreelances();
        } else {
            $userFreelances = $this->entityManager->getRepository(Booking::class)->getUserMatch($user);
        }

        $userAffecteds = [];
        foreach ($mission->getExclusives() as $exclusive) {
            $userAffecteds[] = $exclusive->getUser();
        }
        $started = $mission->getStarted() ? $mission->getStarted()->format('d/m/Y H:i') : '';
        $ended = $mission->getEnded() ? $mission->getEnded()->format('d/m/Y H:i') : '';

        $builder
            // https://symfony.com/doc/current/form/bootstrap5.html
            ->add('title', TextType::class, [
                'label' => 'Poste de la mission: (*)',
                'attr' => ['placeholder' => 'Ex: Éducateur'],
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
                'label' => 'Début mission (*)',
                'attr' => [
                    'class' => 'datepicker'
                ],
                "data" => $started,
                'mapped' => false
            ])
            ->add('fin', TextType::class, [
                'label' => 'Fin mission (*)',
                'attr' => [
                    'class' => 'datepicker'
                ],
                "data" => $ended,
                'mapped' => false
            ])
            ->add('file', FileType::class, [
                'label' => 'Uploader Fiche Poste ( non obligatoire )',
                'required' => false,
                'mapped' => false
            ])
            ->add('published', CheckboxType::class, [
                'required' => false,
                'label' => "Publiée la mission (Si la publication est désactivée, la mission sera enregistrée comme brouillon).",
                'label_attr' => ['class' => 'switch-custom'],
            ])
            ->add('emergency', CheckboxType::class, [
                'required' => false,
                'label' => "Urgent ?",
                'label_attr' => ['class' => 'switch-custom'],
            ])
            ->add('service', EntityType::class, [
                'class' => Service::class,
                'query_builder' => static function (ServiceRepository $repository) use ($user) {
                    return $repository->createQueryBuilder('s')
                        ->innerJoin('s.user', 'u')
                        ->where('u.id = :user_id')
                        ->setParameter('user_id', $user->getId())
                        ->addOrderBy('s.unityName', 'ASC');
                },
                'choice_label' => static function (Service $choice) {
                    return $choice->getUnityName();
                },
                'label' => 'Type de service (Vous pouvez en choisir plusieurs)',
                'multiple' => false,
                'required' => false,
                'attr' => [
                    'class' => 'js-select2 js-event-change-service',
                    'style' => "width: 100%",
                ],
            ])
        ;

        $builder->add('users', EntityType::class, [
            'class' => User::class,
            'query_builder' => static function (UserRepository $repository) use ($userFreelances) {
                return $repository->createQueryBuilder('u')
                    ->where('u.id IN (:freelances)')
                    ->setParameter('freelances', $userFreelances)
                    ->andWhere('u.enabled = :enabled')
                    ->andWhere('u.roles LIKE :roles')
                    ->setParameter('roles', '%'.UserConstant::ROLE_FREELANCE.'%')
                    ->setParameter('enabled', true)
                    ;
            },
            'mapped' => false,
            'label' => 'Envoie une notification a ces personnes avec lesquelles vous avez déjà travaillé',
            'choice_label' => function (User $user) {
                return sprintf('%s',
                    $user->nickname()
                );
            },
            'multiple' => true,
            'required' => false,
            'data' => $userAffecteds,
            'attr' => [
                'class' => 'js-select2',
                'style' => "width: 100%",
            ],
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        
//        $builder
//            ->get('service')
//            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit']);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();

        /** @var Mission $mission */
        $mission = $event->getData();

        $form->add('categories', EntityType::class, [
            'class' => Category::class,
            'query_builder' => static function (CategoryRepository $repository) {
                return $repository->createQueryBuilder('t')
                    ->innerJoin('t.parent', 'p')
                    ->where('t.archived = :archived')
                    ->setParameter('archived', false)
                    ->andWhere('t.type = :type')
                    ->setParameter('type', \App\Constant\CategoryType::MISSION)
                    ->addOrderBy('t.title', 'ASC');
            },
            'group_by' => static function (Category $choice) {
                return $choice->getParent()->getTitle();
            },
            'mapped' => true,
            'label' => 'Choisir les critères pour filtrer les candidats (*)',
            'multiple' => true,
            'attr' => [
                'class' => 'js-select2',
                'style' => "width: 100%",
            ],
        ]);

        $this->changeAdresse($form, $mission->getService());
    }

//    public function onPostSubmit(FormEvent $event): void
//    {
//        $form = $event->getForm();
//
//        /** @var Service $service */
//        $service = $form->getData();
//
//        $this->changeAdresse($form->getParent(), $service);
//    }

    public function changeAdresse(FormInterface $form, ?Service $service = null) {

        if ($service) {
            $address = $service->getAddress();
            $zipCode = $service->getZipCode();
            $city = $service->getCity();
            $phone = $service->getPhone();
        } else {
            /** @var User $user */
            $user = $this->security->getUser();

            $address = $user->getAdress();
            $zipCode = $user->getZipCode();
            $city = $user->getCity();
            $phone = $user->getTelephone();
        }

        $form->add('phone', TelType::class, [
                'label' => "Numéro de téléphone",
                'attr' => [
                    'placeholder' => '0606060606'
                ],
                'constraints' => [
                    new Regex('/0\d{9}/')
                ],
                'data' => $phone
            ])
            ->add('address', TextType::class, [
                'attr' => ['placeholder' => '11 Rue chemin de fer'],
                'data' => $address
            ])
            ->add('zipCode', IntegerType::class, [
                'attr' => ['placeholder' => '75015'],
                'data' => $zipCode
            ])
            ->add('city', TextType::class, [
                'attr' => ['placeholder' => 'Paris'],
                'data' => $city
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}
