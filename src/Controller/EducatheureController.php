<?php

namespace App\Controller;

use App\Constant\PublicType;
use App\Entity\Educatheure;
use App\Entity\Reservation;
use App\helper\ArrayHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/educatheure', name: 'app_educatheure_')]
#[IsGranted('ROLE_USER')]
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
    public function newBooking(Educatheure $educatheur, Request $request, EntityManagerInterface $entityManager): Response
    {

        $reservation = $entityManager->getRepository(Reservation::class)->findBy([
            'educatheure' => $educatheur,
            'owner' => $this->getUser()
        ]);

        if (!$reservation) {
            $reservation = new Reservation();
            $reservation->setEducatheure($educatheur);
            $reservation->setOwner($this->getUser());

            $entityManager->persist($reservation);
        }
        $reservation->setNote($request->get('note'));
        $reservation->setTimeSlot($request->get('publics'));

        if ($request->get('dateSlot')) {
            $reservation->setDateSlot(new \DateTime($request->get('dateSlot')));
        }

        $entityManager->flush();

        $this->addFlash('success', 'Votre demande de réservation à bel a bien été soumise.');

        return $this->redirectToRoute('app_educatheure_show', ['slug' => $educatheur->getSlug()]);
    }
}
