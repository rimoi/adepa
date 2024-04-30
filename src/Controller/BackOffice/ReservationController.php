<?php

namespace App\Controller\BackOffice;

use App\Constant\ReservationType;
use App\Entity\NewRequest;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/office/reservation', name: 'admin_reservation_')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $reservations = $reservationRepository->findBy([], ['id' => 'DESC']);
        } else {
            $reservations = $reservationRepository->findBy(['affected' => $this->getUser(), 'status' => ReservationType::ACCEPTED], ['id' => 'DESC']);
        }

        return $this->render('back_office/reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/validate/{id}', name: 'validate', methods: ['POST'], options: ['expose' => true])]
    public function validate(Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $reservation->setStatus(ReservationType::ACCEPTED);

        $reservation->setAffected($this->getUser());

        foreach ($reservation->getUsers() as $user) {
            $reservation->removeUser($user);
        }

        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/request/{id}', name: 'request_validate', methods: ['POST'], options: ['expose' => true])]
    public function requestValidate(NewRequest $newRequest, EntityManagerInterface $entityManager): Response
    {
        $educatheur = $newRequest->getEducatheur();

        $educatheur->addUser($newRequest->getUser());

        $entityManager->remove($newRequest);

        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/request/{id}', name: 'request_refused', methods: ['POST'], options: ['expose' => true])]
    public function requestRefused(NewRequest $newRequest, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($newRequest);

        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ReservationRepository $reservationRepository): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationRepository->save($reservation, true);

            return $this->redirectToRoute('admin_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_office/reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('back_office/reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservationRepository->save($reservation, true);

            return $this->redirectToRoute('admin_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_office/reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, ReservationRepository $reservationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $reservationRepository->remove($reservation, true);
        }

        return $this->redirectToRoute('admin_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
