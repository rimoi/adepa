<?php

namespace App\Controller\BackOffice;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Repository\BookingRepository;
use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/candidat', name: 'admin_candidat_')]
class CandidatController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        Request $request,
        MissionRepository $missionRepository
    ): Response
    {

        $missions = $missionRepository->findBy([
            'booked' => true,
            'archived' => false,
            'user' => $this->getUser()
        ], ['id' => 'DESC']);
      
        $bookings = [];
        foreach ($missions as $mission) {
            foreach ($mission->getBookings() as $booking) {
                if (!$booking->isArchived()) {
                    $bookings[] = $booking;
                }
            }
        }


        return $this->renderForm('back_office/candidat/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    #[Route('/{slug}/show', name: 'show')]
    public function show(
        Request $request,
        User $user
    ): Response
    {
        $form = $this->createForm(ProfileFormType::class, $user, ['show_experience' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $this->addFlash('danger', "Vous n'avez pas le droit de soumettre ce fomulaire !");

            return $this->redirectToRoute($request->attributes->get('_route'));
        }

        return $this->renderForm('back_office/candidat/show.html.twig', [
            'form' => $form,
            'user' => $user,
            'path' => $this->getParameter('app.image_directory')
        ]);
    }
}
