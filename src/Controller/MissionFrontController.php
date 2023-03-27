<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Mission;
use App\Form\SearchType;
use App\helper\ArrayHelper;
use App\Indexation\MissionIndexation;
use App\Service\NotificationService;
use App\Service\SearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\File as FileEntity;

class MissionFrontController extends AbstractController
{
    #[Route('/missions', name: 'front_mission')]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager,
        SearchService $searchService
    ): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $missions = [];

        $search = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $categories = $form->get('categories')->getData();
            $term = $form->get('q')->getData();

            if ($categories->toArray() || $term) {
                $search = true;
                $missions = $searchService->search($categories->toArray(), $term);
            }
        }

        if (!$missions && !$search) {
            $missions = $entityManager->getRepository(Mission::class)->findBy([
                'archived' => false,
                'published' => true,
                'booked' => false
            ], ['started' => 'ASC']);
        }


        $all_missions = [
            'urgent' => [],
            'normal' => []
        ];

        foreach ($missions as $mission) {
            if ($mission->getStarted() < (new \DateTime('now', new \DateTimeZone('Europe/Paris')))) {
                continue;
            }

            if (
                $mission->getStarted() <= (new \DateTime('+1 week'))
            ) {
                $all_missions['urgent'][] = $mission;
            } else {
                $all_missions['normal'][] = $mission; 
            }
        }
      
        return $this->renderForm('mission_front/index.html.twig', [
            'missions' => $all_missions,
            'form' => $form
        ]);
    }

    #[Route('/{slug}/afficher', name: 'front_mission_show')]
    #[Route('/{id}/see', name: 'front_mission_show_with_id')]
    public function show(Request $request, EntityManagerInterface $em, ?string $slug = null, ?int $id = null): Response
    {

        if ($id) {
            $mission = $em->getRepository(Mission::class)->findOneBy(['id' => $id]);
        } elseif ($slug) {
            $mission = $em->getRepository(Mission::class)->findOneBy(['slug' => $slug]);
        } else {
            throw $this->createNotFoundException('Mission non trouvé');
        }

        if (!$mission) {
            throw $this->createNotFoundException('Mission non trouvé');
        }

        $userConnectedReserved = '';

        if ($mission->isBooked()) {
            foreach ($mission->getBookings() as $booking) {
                if (
                    !$booking->isArchived()
                    && $this->getUser()
                    && $booking->getUser()
                    && $booking->getUser()->getId() === $this->getUser()->getId()
                ) {
                    $userConnectedReserved = $booking->getCreatedAt()->format('d/m/Y h:i');
                }
            }

            if (!$userConnectedReserved) {
                $this->addFlash('danger', 'Cette mission à déjà été réservé !');

                return $this->redirectToRoute('front_mission');
            }
        }

        return $this->render('mission_front/show.html.twig', [
            'mission' => $mission,
            'user_connected_reserved' => $userConnectedReserved,
            'retraction' => $mission->getStarted() > (new \DateTime('+48 hours', new \DateTimeZone('Europe/Paris')))
        ]);
    }

    #[Route('/{slug}/download', name: 'front_mission_download')]
    public function download(Request $request, Mission $mission): BinaryFileResponse
    {
        $file = new File($this->getParameter('app.image_directory').'/'.$mission->getFile()->getName());

        return $this->file($file);
    }

    #[Route('/{id}/consuler', name: 'front_mission_consulter')]
    public function consuler(Request $request, FileEntity $fichier): BinaryFileResponse
    {
        $file = new File($this->getParameter('app.image_directory').'/'.$fichier->getName());

        return $this->file($file, $fichier->getName() , ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/{slug}/candidate', name: 'front_mission_candidate')]
    public function candidate(
        Mission $mission,
        EntityManagerInterface $em,
        NotificationService $notificationService,
        MissionIndexation $missionIndexation
    ): Response
    {
        $booking = $em->getRepository(Booking::class)->findOneBy([
            'archived' => false,
            'user' => $this->getUser(),
            'mission' => $mission,
        ]);

        if ($booking) {
            $this->addFlash('danger', 'Cette mission à déjà été réservé !');

            return $this->redirectToRoute('front_mission');
        }

        $booking = new Booking();
        $booking->setMission($mission);
        $booking->setUser($this->getUser());

        $em->persist($booking);

        $mission->setBooked(true);

        $em->flush();

        $notificationService->sendEmailToFreelanceCandidate($this->getUser(), $mission);
        $notificationService->sendEmailToClient($this->getUser(), $mission);

        // suppression de l'index de meilysearch
        $missionIndexation->delete($mission);

        $this->addFlash('success', sprintf('Réservation mission `%s` à bien été effectuée !', $mission->getTitle()));

        return $this->redirectToRoute('front_mission');
    }

    #[Route('/{slug}/cancel-booking', name: 'front_mission_cancel_booking')]
    public function cancelBooking(
        Mission $mission,
        EntityManagerInterface $em,
        NotificationService $notificationService,
        MissionIndexation $missionIndexation
    ): Response
    {
        $mission->setBooked(false);

        $bookings = $mission->getBookings();

        foreach ($bookings as $booking) {
            if ($booking->getUser()->getId() === $this->getUser()->getId()) {
                $booking->setArchived(true);

                $em->flush();
            }
        }

        $this->addFlash('success', sprintf('La mission `%s` à bien été libérée !', $mission->getTitle()));

        $notificationService->sendEmailToFreelanceCancel($this->getUser(), $mission);
        $notificationService->sendEmailToClientCancel($this->getUser(), $mission);

        // création de l'index pour meilysearch
        $missionIndexation->create($mission);

//        $this->addFlash('danger',
//            sprintf("La mission n'a pas pu être libérée ( Veuillez contactez votre administrateur en renseignant le numéro suivante : %d )", $mission->getId())
//        );

        return $this->redirectToRoute('front_mission');
    }
}
