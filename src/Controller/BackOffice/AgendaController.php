<?php

namespace App\Controller\BackOffice;

use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/admin/agenda', name: 'admin_agenda_')]
class AgendaController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(BookingRepository $bookingRepository, UrlGeneratorInterface $urlGenerator): Response
    {
        $bookings = $bookingRepository->findBy([
            'user' => $this->getUser(),
            'archived' => false
        ]);
        
        $missions = [];
        foreach ($bookings as $booking) {
            $missions[] = [
                'id' => $booking->getMission()->getId(),
                'start' => $booking->getMission()->getStarted()->format('Y-m-d H:i:s'),
                'end' => $booking->getMission()->getEnded()->format('Y-m-d H:i:s'),
                'title' => $booking->getMission()->getTitle(),
                'description' => $booking->getMission()->getContent(),
                'address' => $booking->getMission()->getAddress(),
                'city' => $booking->getMission()->getCity(),
                'zipCode' => $booking->getMission()->getZipCode(),
                'url' => $urlGenerator->generate('front_mission_show', ['slug' => $booking->getMission()->getSlug()])
            ];
        }
        $missions = json_encode($missions);


        return $this->render('back_office/agenda/index.html.twig', compact('missions'));
    }
}
