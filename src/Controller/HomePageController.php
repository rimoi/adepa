<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Mission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(EntityManagerInterface $entityManager)
    {
        $filter = [
            'published' => true,
            'archived' => false,
        ];

        $missions = $entityManager->getRepository(Mission::class)->findBy($filter + ['booked' => false], ['started' => 'ASC']) ;

        $all_missions = [];

        foreach ($missions as $mission) {
            if ($mission->getStarted() < (new \DateTime('now', new \DateTimeZone('Europe/Paris')))) {
                continue;
            }

            if (
                $mission->getStarted() <= (new \DateTime('+1 week'))
            ) {
                $all_missions[] = $mission;
            }
        }
        
        $articles = $entityManager->getRepository(Article::class)->findBy($filter);

        return $this->render('homepage/homepage.html.twig', [
            'articles' => $articles,
            'missions' => array_slice($all_missions, 0, 3),
            'all_mission' => count($all_missions),
        ]);
    }

    #[Route('/calendrier', name: 'calendar')]
    public function calendar(ProductRepository $product)
    {
        $events = $product->findAll();
        
        $rdvs = [];
// Querybuilder pour un affichage selon user. 
        foreach($events as $event){
            $rdvs[]= [
                'id' => $event->getId(),
                'start' => $event->getStartDate()->format("Y-m-d H:i:s"),
                'end' => $event->getEndDate()->format("Y-m-d H:i:s"),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
            ];
        }

        $data = json_encode($rdvs);
        return $this->render('calendar.html.twig', compact('data')); 
    }
}