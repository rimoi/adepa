<?php

namespace App\Controller\BackOffice;

use App\Constant\UserConstant;
use App\Entity\User;
use App\Form\ProfileFormType;
use App\Form\SendInvitationFormType;
use App\helper\PasswordHelper;
use App\Service\NotificationService;
use App\Service\QualificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;


#[Route('/admin', name: 'admin_user_')]
#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_MANAGER")'))]
class UserController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(EmailVerifier $emailVerifier, UrlGeneratorInterface $urlGenerator)
    {
        $this->emailVerifier = $emailVerifier;
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/users', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $users = [
            'clients' => [],
            'responsables' => [],
            'salaries' => [],
            'freelances' => [],
        ];

        $allUsers = $entityManager->getRepository(User::class)->findBy(['archived' => false], ['id' => 'DESC']);
        foreach ($allUsers as $user) {
            if ($user->hasRole(UserConstant::ROLE_CLIENT)) {
                $users['clients'][] = $user;
            }

            if ($user->hasRole(UserConstant::ROLE_EMPLOYE)) {
                $users['salaries'][] = $user;
            }

            if ($user->hasRole(UserConstant::ROLE_MANAGER)) {
                $users['responsables'][] = $user;
            }

            if ($user->hasRole(UserConstant::ROLE_FREELANCE)) {
                $users['freelances'][] = $user;
            }

            
        }

        return $this->render('back_office/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/new', name: 'new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        QualificationService $qualificationService
    ): Response
    {
        $user = new User();
        $form = $this->createForm(
            ProfileFormType::class,
            $user,
            [
                'new_user' => true,
                'edit_user' => true
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$form->get('plainPassword')->getData()) {
                $form->get('plainPassword')->addError(new FormError('Un mot de passe devrait être renseigné !'));
                return $this->render('back_office/user/new.html.twig', [
                    'form' => $form,
                ]);
            }

            if ($roles = $form->get('roles')->getData()) {
                $r = [];
                foreach ($roles as $role) {
                    $r[] = UserConstant::asString($role);
                }

                $user->setRoles($r);
            }
            $qualificationService->addElement($form, 'cni');
            $qualificationService->addElement($form, 'criminalRecord');
            $qualificationService->addElement($form, 'permisConduite');
            $qualificationService->addElement($form, 'iban');
            $qualificationService->addElement($form, 'autoentrepriseCertificate');

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);

            $entityManager->flush();

            $this->addFlash('success', "L'utilisateur est créé avec succès");

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('back_office/user/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/user/{slug}/edit', name: 'edit')]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        QualificationService $qualificationService
    ): Response
    {
        $form = $this->createForm(
            ProfileFormType::class,
            $user,
            [
                'edit_user' => true,
                'show_experience' => true
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('plainPassword')->getData()) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            if ($roles = $form->get('roles')->getData()) {
                $r = [];
                foreach ($roles as $role) {
                    $r[] = UserConstant::asString($role);
                }

                $user->setRoles($r);
            }

            $qualificationService->addElement($form, 'cni');
            $qualificationService->addElement($form, 'criminalRecord');
            $qualificationService->addElement($form, 'permisConduite');
            $qualificationService->addElement($form, 'iban');
            $qualificationService->addElement($form, 'autoentrepriseCertificate');

            $entityManager->flush();

            $this->addFlash('success', 'Les modifications ont bien été enregistrées !');

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('back_office/user/edit.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }

    #[Route('/user/{slug}/archived', name: 'archived')]
    public function archived(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {

        $archived = $user->isArchived() ? 'désarchiver' : 'archivé';

        $user->setArchived(!$user->isArchived());

        $entityManager->flush();

        $this->addFlash('success', sprintf('Utilisateur %s à bien été %s !', $user->nickname(), $archived));

        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/user/{slug}/activate', name: 'activate', methods: ['POST'], options: ['expose' => true])]
    public function activate(
        User $user,
        EntityManagerInterface $entityManager,
        NotificationService  $notificationService
    ): Response
    {
        $user->setEnabled(true);
        $user->setIsVerified(true);

        $notificationService->activateAccount($user);

        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/user/send', name: 'send_invitation')]
    #[IsGranted(new Expression('is_granted("ROLE_DIRECTION") or is_granted("ROLE_MANAGER")'))]
    public function sendInvitation(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        string $mailerSender
    ): Response
    {
        $user = new User();
        $form = $this->createForm(SendInvitationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $options = ['min' => 8, 'max' => 12];
            $password = PasswordHelper::generate($options);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            
            $status = $form->get('statut')->getData();

            $user->setRoles([$status]);

            $services = $form->get('services')->getData();

            if (!$services->isEmpty()) {
                foreach($services as $service) {
                    $user->addService($service);
                    //$service->setUser($user);
                }
            }

            $sender = $this->getUser()->nickname();

            //dump($user);exit;
            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($mailerSender, 'LES EXTRAS'))
                    ->to($user->getEmail())
                    ->subject('LES EXTRAS - Invitation :' . $password)
                    ->htmlTemplate('back_office/invitation/send_email.html.twig')
                    ->context(
                        [
                            'password' => $password,
                            'sender' => $sender
                        ])
            );

            $notice = sprintf(
                'Vôtre invitation a été envoyé avec succès. <br/> Nous avons envoyé un email d’activation à l’adresse <b>%s</b>.',
                $user->getEmail()
            );
            $this->addFlash('success', $notice);

            return $this->redirectToRoute('admin_user_send_invitation');
        }

        return $this->render('back_office/user/send_invitation.html.twig', [
            'form' => '',
            'invitation_form' => $form,
        ]);
    }
}
