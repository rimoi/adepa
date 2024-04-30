<?php

namespace App\Controller\BackOffice;

use App\Constant\UserConstant;
use App\Entity\File;
use App\Entity\User;
use App\Form\ProfileClientFormType;
use App\Form\ProfileFormType;
use App\helper\ArrayHelper;
use App\helper\FormHelper;
use App\Service\FileUploader;
use App\Service\QualificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_profile_')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'index')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        QualificationService $qualificationService
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->hasRole(UserConstant::ROLE_CLIENT)) {
            $form = $this->createForm(ProfileClientFormType::class, $user, ['show_service' => true]);
        } else {
            $form = $this->createForm(ProfileFormType::class, $user, ['show_experience' => true]);
        }
        $form->handleRequest($request);

        $errors = null;

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$user->hasRole(UserConstant::ROLE_CLIENT)) {
                $qualificationService->addElement($form, 'cni');
                $qualificationService->addElement($form, 'permisConduite');
                $qualificationService->addElement($form, 'criminalRecord');
                $qualificationService->addElement($form, 'iban');
                $qualificationService->addElement($form, 'autoentrepriseCertificate');

                // Decommenter quand on rajout le CV
                //$qualificationService->addElement($form, 'file');

                $qualificationService->addExperience($form, 'experiences');
                $qualificationService->addExperience($form, 'qualifications');
            } else {
                $qualificationService->addExperience($form, 'services');
            }

            if ($form->has('days')) {
                if ($days = $form->get('days')->getData()) {
                    $user->setDays($days);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Les modifications ont bien été enregistrées !');

            return $this->redirectToRoute('admin_dash_board_index');
        } elseif ($form->isSubmitted() && !$form->isValid()) {

            $errors = FormHelper::getErrorsFromForm($form, true);

        }

        $params = [
            'form' => $form,
            'errors' => $errors,
            'show_updaded_image' => !!$errors,
        ];

        if ($user->hasRole(UserConstant::ROLE_CLIENT)) {
            return $this->renderForm('back_office/profile/profil_client.html.twig', $params);
        }

        return $this->renderForm('back_office/profile/index.html.twig', $params);
    }
}
