<?php

namespace App\Controller\BackOffice;

use App\Constant\NotificationConstant;
use App\Constant\UserConstant;
use App\Entity\Booking;
use App\Entity\Exclusive;
use App\Entity\Mission;
use App\Entity\Service;
use App\Form\MissionType;
use App\Form\ValidateTimeType;
use App\Repository\BookingRepository;
use App\Repository\MissionRepository;
use App\Service\NotificationService;
use App\Service\QualificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/mission', name: 'admin_mission_')]
class MissionController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(MissionRepository $missionRepository): Response
    {
      
        if ($this->isGranted('ROLE_ADMIN')) {
            $missions = $missionRepository->findBy(['archived' => false], ['id' => 'DESC']);
        } else {
            $missions = $missionRepository->findBy(['archived' => false, 'user' => $this->getUser()], ['id' => 'DESC']);
        }

        return $this->render('back_office/mission/index.html.twig', [
            'missions' => $missions
        ]);
    }

    #[Route('/mes-missions', name: 'my_missions', methods: ['GET'])]
    public function missions(BookingRepository $bookingRepository): Response
    {
        $bookings = $bookingRepository->findBy([
            'user' => $this->getUser(),
            'archived' => false
        ]);

        $missions = [];
        foreach ($bookings as $booking) {
            $missions[$booking->getCreatedAt()->format('d/m/Y H:i:s')] = $booking->getMission();
        }

        return $this->render('back_office/mission/missions.html.twig', [
            'missions' => $missions,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,  QualificationService $qualificationService, MissionRepository $missionRepository, NotificationService $notificationService): Response
    {
        if (!$this->getUser()->isEnabled()) {
            $this->addFlash('danger', "Vous ne pouvez pas proposer une mission tant que votre profil n'est pas validé par un administrateur !");

            return $this->redirectToRoute('admin_mission_index');
        }

        $mission = new Mission();

        $form = $this->createForm(
            MissionType::class,
            $mission
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($dateDebut = $form->get('debut')->getData()) {
                $dateDebut = str_replace('/', '-', $dateDebut);
                $mission->setStarted(new \DateTimeImmutable($dateDebut, new \DateTimeZone('Europe/Paris')));
            }
            if ($dateFin = $form->get('fin')->getData()) {
                $dateFin = str_replace('/', '-', $dateFin);
                $mission->setEnded(new \DateTimeImmutable($dateFin, new \DateTimeZone('Europe/Paris')));
            }
            if ($mission->getEnded() <= $mission->getStarted()) {
                $form->get('fin')->addError(new FormError('La date de fin ne peux pas être avant la date de debut !'));

                return $this->renderForm('back_office/mission/new.html.twig', [
                    'mission' => $mission,
                    'form' => $form,
                ]);
            }

            $users = [];

            if ($form->has('users')) {
                $users = $form->get('users')->getData();

                if ($users) {
                    $mission->setEmergency(true);

                    foreach ($users as $user) {
                        /*
                             Si la personne qui Crée la mission demande des personnes précisément
                             sur le formulaire, seuls eux reçoivent les messages MISSION DISPO et si
                             mission non prise en 2 jours, l’envoi de la mission est envoyé à toutes les
                             personnes ayant le profil
                         */
                        $exclusive = new Exclusive();
                        $exclusive->setMission($mission);
                        $exclusive->setUser($user);

                        $entityManager->persist($exclusive);
                    }
                }
            }
            $mission->setUser($this->getUser());

            $qualificationService->addElement($form, 'file');

            $missionRepository->save($mission, true);


            $notificationService->infoUserMission($mission, NotificationConstant::EMAIL, $users);

            if ($mission->isEmergency()) {
                $notificationService->infoUserMission($mission, NotificationConstant::WATHAPPS, $users);
            }

            $this->addFlash('success', sprintf('Mission `%s` à été crée !', $mission->getTitle()));

            return $this->redirectToRoute('admin_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_office/mission/new.html.twig', [
            'mission' => $mission,
            'form' => $form,
        ]);
    }

    #[Route('/get-info/service/{id}', name: 'get_info_service', methods: ['GET'], options: ['expose' => true])]
    public function getInfoMission(Service $service): Response
    {
        return new JsonResponse([
           'address' => $service->getAddress(),
           'zipCode' => $service->getZipCode(),
           'city' => $service->getCity(),
           'phone' => $service->getPhone(),
        ]);
    }

    #[Route('/{slug}/show', name: 'show', methods: ['GET'])]
    public function show(Mission $mission): Response
    {
        return $this->render('back_office/mission/show.html.twig', [
            'mission' => $mission,
        ]);
    }

    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Mission $mission,
        MissionRepository $missionRepository,
        QualificationService $qualificationService,
        NotificationService $notificationService,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $qualificationService->addElement($form, 'file');

            if ($dateDebut = $form->get('debut')->getData()) {
                $dateDebut = str_replace('/', '-', $dateDebut);
                $mission->setStarted(new \DateTimeImmutable($dateDebut, new \DateTimeZone('Europe/Paris')));
            }
            if ($dateFin = $form->get('fin')->getData()) {
                $dateFin = str_replace('/', '-', $dateFin);
                $mission->setEnded(new \DateTimeImmutable($dateFin, new \DateTimeZone('Europe/Paris')));
            }

            if ($mission->getEnded() <= $mission->getStarted()) {
                $form->get('fin')->addError(new FormError('La date de fin ne peux pas être avant la date de début !'));
                
                return $this->renderForm('back_office/mission/edit.html.twig', [
                    'mission' => $mission,
                    'form' => $form,
                ]);
            }

            if ($form->has('users')) {
                $users = $form->get('users')->getData();

                if ($users) {
                    $mission->setEmergency(true);

                    $userExists = $entityManager->getRepository(Exclusive::class)->findBy(['mission' => $mission]);

                    foreach ($userExists as $userExist) {
                        $entityManager->remove($userExist);
                    }

                    foreach ($users as $user) {
                        $exclusive = new Exclusive();
                        $exclusive->setMission($mission);
                        $exclusive->setUser($user);

                        $entityManager->persist($exclusive);
                    }

//                    $notificationService->infoUserMission($mission, NotificationConstant::SMS, $users);
                    $notificationService->infoUserMission($mission, NotificationConstant::EMAIL, $users);
                    $notificationService->infoUserMission($mission, NotificationConstant::WATHAPPS, $users);
                }
            }

            $missionRepository->save($mission, true);


            $this->addFlash('success', sprintf('Mission `%s` modifiée !', $mission->getTitle()));

            return $this->redirectToRoute('admin_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_office/mission/edit.html.twig', [
            'mission' => $mission,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/archived', name: 'archived', methods: ['POST', 'GET'])]
    public function delete(Mission $mission, EntityManagerInterface $entityManager): Response
    {
        $mission->setArchived(true);
        $mission->setEmergency(false);
        $mission->setBooked(false);

        $entityManager->flush();

        $this->addFlash('success', sprintf('Mission `%s` archivée !', $mission->getTitle()));

        return $this->redirectToRoute('admin_mission_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/cancel-booking', name: 'cancel_booking')]
    public function cancelBooking(Booking $booking, EntityManagerInterface $em, NotificationService $notificationService): Response
    {
        $mission = $booking->getMission();
        $mission->setBooked(false);

        $booking->setArchived(true);

        $this->addFlash('success', sprintf('La mission `%s` a bien été annuler !', $mission->getTitle()));

        $em->flush();

        // à changer ces deux lma
        $notificationService->sendEmailToFreelanceCancel($booking->getUser(), $mission, 'mailing/admin/acknowledge_cancel_candidate.html.twig');
        $notificationService->sendEmailToClientCancel($booking->getUser(), $mission, 'mailing/admin/candidate_cancel.html.twig');

        return $this->redirectToRoute('admin_candidat_index');
    }

    #[Route('/check/terminate/booking', name: 'check_terminate_booking')]
    public function checkTerminateBooking(EntityManagerInterface $em, NotificationService $notificationService): Response
    {
        $bookings = $em->getRepository(Booking::class)->getTerminateBooking();

        foreach ($bookings as $booking) {
            $notificationService->sendEmailToFreelanceConfirmTime($booking, $booking->getMission(), 'mailing/admin/freelance_confirm_date.html.twig');
        }

        $this->addFlash('success', sprintf('%d emails envoyés au Freelances', count($bookings)));

        return $this->redirectToRoute('admin_dash_board_index');
    }


    #[Route('/{id}/validate/first/step', name: 'validate_time_first_step')]
    public function validateTimeFirstStep(Request $request, Booking $booking, EntityManagerInterface $em, NotificationService $notificationService): Response
    {
        if (!$booking->getConfirmStarted()) {
            $booking->setConfirmStarted($booking->getMission()->getStarted());
            $booking->setConfirmEnded($booking->getMission()->getEnded());
        }
        
        $form = $this->createForm(ValidateTimeType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($dateDebut = $form->get('debut')->getData()) {
                $dateDebut = str_replace('/', '-', $dateDebut);
                $booking->setConfirmStarted(new \DateTime($dateDebut, new \DateTimeZone('Europe/Paris')));
            }
            if ($dateFin = $form->get('fin')->getData()) {
                $dateFin = str_replace('/', '-', $dateFin);
                $booking->setConfirmEnded(new \DateTime($dateFin, new \DateTimeZone('Europe/Paris')));
            }

            if ($booking->getConfirmEnded() <= $booking->getConfirmStarted()) {
                $form->get('fin')->addError(new FormError('La date de fin ne peux pas être avant la date de début !'));

                return $this->renderForm('back_office/mission/confirm.html.twig', [
                    'form' => $form,
                    'booking' => $booking
                ]);
            }

            $this->addFlash('success', sprintf('Les dates pour la mission `%s` ont bien été enregistrée !', $booking->getMission()->getTitle()));

            $em->flush();

            $notificationService->sendEmailToClientConfirmTime($booking, $booking->getMission(), 'mailing/admin/confirm_date.html.twig');

            return $this->redirectToRoute('admin_dash_board_index');
        }

        return $this->renderForm('back_office/mission/confirm.html.twig', [
            'form' => $form,
            'booking' => $booking
        ]);
    }

    #[Route('/{id}/validate/second/step', name: 'validate_time_second_step')]
    public function validateTimeSecondStep(Request $request, Booking $booking, EntityManagerInterface $em, NotificationService $notificationService): Response
    {
        $form = $this->createForm(ValidateTimeType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $isSame = true;
            $format = "Y-m-d H";

            if ($dateDebut = $form->get('debut')->getData()) {
                $dateDebut = str_replace('/', '-', $dateDebut);

                $dateDebut = new \DateTime($dateDebut, new \DateTimeZone('Europe/Paris'));

                if ($dateDebut->format($format) !== $booking->getConfirmStarted()->format($format)) {
                    $isSame = false;
                }

            }
            if ($dateFin = $form->get('fin')->getData()) {
                $dateFin = str_replace('/', '-', $dateFin);

                $dateFin = new \DateTime($dateFin, new \DateTimeZone('Europe/Paris'));

                if ($dateFin->format($format) !== $booking->getConfirmEnded()->format($format)) {
                    $isSame = false;
                }
            }

            if ($isSame) {
                $booking->setConfirmStarted($dateDebut);
                $booking->setConfirmEnded($dateFin);
                $booking->setValidate(true);
                $booking->setArchived(true);

                $notificationService->sendEmailToClientConfirmFine($booking, $booking->getMission(), 'mailing/admin/confirm_fine.html.twig');
            } else {

                $tab = [
                    'client' => [
                        'debut' => $dateDebut,
                        'fin' => $dateFin,
                    ],
                    'freelance' => [
                        'debut' => $booking->getConfirmStarted(),
                        'fin' => $booking->getConfirmEnded(),
                    ]
                ];

                $notificationService->sendEmailAdminConflitValidateTime($booking, $booking->getMission(), $tab);
            }

            $this->addFlash('success', sprintf('Les dates pour la mission `%s` ont bien été enregistrée !', $booking->getMission()->getTitle()));

            $em->flush();

            return $this->redirectToRoute('admin_dash_board_index');
        }

        return $this->renderForm('back_office/mission/confirm.html.twig', [
            'form' => $form,
            'booking' => $booking
        ]);
    }

    #[Route('/{id}/validate/last/step', name: 'validate_time_last_step')]
    public function validateTimeLastStep(Request $request, Booking $booking, EntityManagerInterface $em, NotificationService $notificationService): Response
    {
        $form = $this->createForm(ValidateTimeType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($dateDebut = $form->get('debut')->getData()) {
                $dateDebut = str_replace('/', '-', $dateDebut);
                $booking->setConfirmStarted(new \DateTime($dateDebut, new \DateTimeZone('Europe/Paris')));
            }
            if ($dateFin = $form->get('fin')->getData()) {
                $dateFin = str_replace('/', '-', $dateFin);
                $booking->setConfirmEnded(new \DateTime($dateFin, new \DateTimeZone('Europe/Paris')));
            }

            if ($booking->getConfirmEnded() <= $booking->getConfirmStarted()) {
                $form->get('fin')->addError(new FormError('La date de fin ne peux pas être avant la date de début !'));

                return $this->renderForm('back_office/mission/confirm.html.twig', [
                    'form' => $form,
                    'booking' => $booking
                ]);
            }

            $booking->setValidate(true);
            $booking->setArchived(true);


            $this->addFlash('success', sprintf('Les dates pour la mission `%s` ont bien été enregistrée et les emails sont envoyés !', $booking->getMission()->getTitle()));

            $em->flush();

            $notificationService->sendEmailToClientConfirmFine($booking, $booking->getMission());

            return $this->redirectToRoute('admin_dash_board_index');
        }

        return $this->renderForm('back_office/mission/confirm.html.twig', [
            'form' => $form,
            'booking' => $booking
        ]);
    }
}
