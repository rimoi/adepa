<?php

namespace App\Controller\BackOffice;

use App\Constant\UserConstant;
use App\Entity\User;
use App\Form\ProfileFormType;
use App\Service\QualificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin', name: 'admin_user_')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $users = [
            'clients' => [],
            'freelances' => []
        ];

        $allUsers = $entityManager->getRepository(User::class)->findBy(['archived' => false], ['id' => 'DESC']);
        foreach ($allUsers as $user) {
            if ($user->hasRole(UserConstant::ROLE_CLIENT)) {
                $users['clients'][] = $user;
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
                return $this->renderForm('back_office/user/new.html.twig', [
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

        return $this->renderForm('back_office/user/new.html.twig', [
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

        return $this->renderForm('back_office/user/edit.html.twig', [
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
        EntityManagerInterface $entityManager
    ): Response
    {
        $user->setEnabled(true);

        $entityManager->flush();

        return $this->json(['success' => true]);
    }
}
