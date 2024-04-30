<?php

namespace App\Controller;

use App\Constant\PublicType;
use App\Entity\Category;
use App\Entity\Educatheure;
use App\Entity\NewRequest;
use App\Entity\Reservation;
use App\Entity\Service;
use App\helper\ArrayHelper;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/educatheure', name: 'app_educatheure_')]
class EducatheureController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
      
        $educatheurs = $entityManager->getRepository(Educatheure::class)->search($request->query->all());

        $educatheursAll = $entityManager->getRepository(Educatheure::class)->findBy(['archived' => false, 'published' => true]);
        
        $cities = ArrayHelper::createAssociativeArray($educatheursAll, 'zipCode', 'city');

        $categories = [];
        foreach ($educatheursAll as $educateur) {
            foreach ($educateur->getCategories() as $category) {
                $categories[$category->getId()] = $category->getTitle();
            }
        }

        $queries = $request->query->all();
       
        if ($others = $request->get('other_queries')) {
            $others = json_decode($others, true);

            $request->query->remove('other_queries');

            $queries = $request->query->all();

            $queries = array_merge($others, $queries);

            foreach ($queries as $index => $query) {
                $request->query->remove($index);
                $request->query->set($index, $query);
            }

        }

        return $this->render('educatheure/index.html.twig', [
            'educatheures' => $educatheurs,
            'photo_directory' => $this->getParameter('app.relative_path.image_directory'),
            'cities' => $cities,
            'categories' => $categories,
            'publics' => PublicType::REVERSE_MAP,
            'queries' => json_encode($queries),
        ]);
    }

    #[Route('/show/{slug}', name: 'show')]
    public function show(Educatheure $educatheur): Response
    {
        return $this->render('educatheure/show.html.twig', [
            'educatheure' => $educatheur,
            'photo_directory' => $this->getParameter('app.relative_path.image_directory'),
        ]);
    }

    #[Route('/new-booking/{slug}', name: 'new_booking', methods: ['POST'])]
    public function newBooking(Educatheure $educatheur, Request $request, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        $reservation = new Reservation();
        $reservation->setEducatheure($educatheur);
        $reservation->setOwner($this->getUser());

        $entityManager->persist($reservation);

        $reservation->setNote($request->get('note'));
        if ( $publics = $request->get('publics')) {
            $categories =  $entityManager->getRepository(Category::class)->findBy(['id' => $publics]);
            foreach ($categories as $category) {
                $reservation->addCategory($category);
            }
        }

        foreach ($educatheur->getUsers() as $user) {
            $reservation->addUser($user);
        }

        if ($request->get('startedAt')) {
            $startedAt = \DateTime::createFromFormat('d/m/Y H:i', $request->get('startedAt'));
            $reservation->setStartedAt($startedAt);
        }
        if ($request->get('endAt')) {
            $dateEndAt = \DateTime::createFromFormat('d/m/Y H:i', $request->get('endAt'));
            $reservation->setEndAt($dateEndAt);
        }

        if ($request->get('adresse1')) {
            $service = new Service();
            $service->setAddress($request->get('adresse1'));
            $service->setZipCode($request->get('zipCode1'));
            $service->setPublic($request->get('poste1'));
            $service->setCity($request->get('city1'));
            $service->setContactName($request->get('name1'));
            $service->setPhone($request->get('phone1'));

            $reservation->addService($service);
            $service->setReservation($reservation);

            $entityManager->persist($service);
        }

        if ($request->get('adresse2')) {
            $service = new Service();
            $service->setAddress($request->get('adresse2'));
            $service->setZipCode($request->get('zipCode2'));
            $service->setCity($request->get('city2'));
            $service->setContactName($request->get('name2'));
            $service->setPhone($request->get('phone2'));

            $reservation->addService($service);

            $service->setReservation($reservation);

            $entityManager->persist($service);
        }

        if ($request->get('intervention')) {
            $reservation->setNumberIntervention($request->get('intervention'));

            $reservation->setPrice($reservation->getNumberIntervention() * 30);
        }

        $entityManager->flush();

        $notificationService->createNotification($reservation);

        $entityManager->flush();

        $this->addFlash('success', 'Votre demande de réservation à bel a bien été soumise.');

        return $this->redirectToRoute('app_educatheure_show', ['slug' => $educatheur->getSlug()]);
    }

    #[Route('/new-request/{slug}', name: 'new_request', methods: ['POST'])]
    #[IsGranted('ROLE_FREELANCE')]
    public function newRequest(Educatheure $educatheur, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        $newRequest = new NewRequest();
        $newRequest->setEducatheur($educatheur);
        $newRequest->setUser($this->getUser());

        $entityManager->persist($newRequest);

        $notificationService->createNewRequest($newRequest);

        $entityManager->flush();

        $this->addFlash('success', 'Votre demande à bel a bien été soumise.');

        return $this->redirectToRoute('app_educatheure_show', ['slug' => $educatheur->getSlug()]);
    }
}
