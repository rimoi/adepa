<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Mission;
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
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $missions = $entityManager->getRepository(Mission::class)->findBy([
            'archived' => false,
            'published' => true,
            'booked' => false
        ], ['started' => 'ASC']);

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

        return $this->render('mission_front/index.html.twig', [
            'missions' => $all_missions,
        ]);
    }

    #[Route('/{slug}/afficher', name: 'front_mission_show')]
    public function show(Request $request, Mission $mission): Response
    {
        $userConnectedReserved = '';

        if ($mission->isBooked()) {
            foreach ($mission->getBookings() as $booking) {
                if (
                    !$booking->isArchived()
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
            'user_connected_reserved' => $userConnectedReserved
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
    public function candidate(Request $request, Mission $mission, EntityManagerInterface $em): Response
    {
        $booking = $em->getRepository(Booking::class)->findBy([
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

        $this->addFlash('success', sprintf('Réservation mission `%s` à bien été effectuée !', $mission->getTitle()));

        return $this->redirectToRoute('front_mission');
    }

    #[Route('/{slug}/cancel-booking', name: 'front_mission_cancel_booking')]
    public function cancelBooking(Mission $mission, EntityManagerInterface $em): Response
    {
        $mission->setBooked(false);

        $bookings = $mission->getBookings();

        foreach ($bookings as $booking) {
            if ($booking->getUser()->getId() === $this->getUser()->getId()) {
                $booking->setArchived(true);

                $this->addFlash('success', sprintf('La mission `%s` à bien été libérée !', $mission->getTitle()));

                $em->flush();

                return $this->redirectToRoute('front_mission');
            }
        }

        $this->addFlash('danger',
            sprintf("La mission n'a pas pu être libérée ( Veuillez contactez votre administrateur en renseignant le numéro suivante : %d )", $mission->getId())
        );

        return $this->redirectToRoute('front_mission');
    }
}
