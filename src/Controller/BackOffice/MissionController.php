<?php

namespace App\Controller\BackOffice;

use App\Constant\NotificationConstant;
use App\Entity\File;
use App\Entity\Mission;
use App\Form\MissionType;
use App\Repository\BookingRepository;
use App\Repository\MissionRepository;
use App\Service\FileUploader;
use App\Service\NotificationService;
use App\Service\QualificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
    public function new(Request $request, QualificationService $qualificationService, MissionRepository $missionRepository, NotificationService $notificationService): Response
    {
        if (!$this->getUser()->isEnabled()) {
            $this->addFlash('danger', "Vous ne pouvez pas proposer une mission tant que votre profil n'est pas validé par un administrateur !");

            return $this->redirectToRoute('admin_mission_index');
        }

        $mission = new Mission();
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($dateDebut = $form->get('debut')->getData()) {
                $dateDebut = str_replace('/', '-', $dateDebut);
                $mission->setStarted(new \DateTimeImmutable($dateDebut));
            }
            if ($dateFin = $form->get('fin')->getData()) {
                $dateFin = str_replace('/', '-', $dateFin);
                $mission->setEnded(new \DateTimeImmutable($dateFin));
            }
            if ($mission->getEnded() <= $mission->getStarted()) {
                $form->get('fin')->addError(new FormError('La date de fin ne peux pas être avant la date de debut !'));

                return $this->renderForm('back_office/mission/new.html.twig', [
                    'mission' => $mission,
                    'form' => $form,
                ]);
            }

            $mission->setUser($this->getUser());

            $qualificationService->addElement($form, 'file');

            $missionRepository->save($mission, true);

            if ($mission->isEmergency()) {
                $notificationService->infoUserMission($mission);
            }

            $this->addFlash('success', sprintf('Mission `%s` à été crée !', $mission->getTitle()));

            return $this->redirectToRoute('admin_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_office/mission/new.html.twig', [
            'mission' => $mission,
            'form' => $form,
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
    ): Response
    {
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $qualificationService->addElement($form, 'file');

            if ($dateDebut = $form->get('debut')->getData()) {
                $dateDebut = str_replace('/', '-', $dateDebut);
                $mission->setStarted(new \DateTimeImmutable($dateDebut));
            }
            if ($dateFin = $form->get('fin')->getData()) {
                $dateFin = str_replace('/', '-', $dateFin);
                $mission->setEnded(new \DateTimeImmutable($dateFin));
            }

            if ($mission->getEnded() <= $mission->getStarted()) {
                $form->get('fin')->addError(new FormError('La date de fin ne peux pas être avant la date de début !'));
                
                return $this->renderForm('back_office/mission/edit.html.twig', [
                    'mission' => $mission,
                    'form' => $form,
                ]);
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

    #[Route('/{slug}/archived', name: 'archived', methods: ['POST'])]
    public function delete(Request $request, Mission $mission, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mission->getId(), $request->request->get('_token'))) {
//            $missionRepository->remove($mission, true);
            $mission->setArchived(!$mission->isArchived());
            $entityManager->flush();

            $this->addFlash('success', sprintf('Mission `%s` archivée !', $mission->getTitle()));

        }

        return $this->redirectToRoute('admin_mission_index', [], Response::HTTP_SEE_OTHER);
    }
}
