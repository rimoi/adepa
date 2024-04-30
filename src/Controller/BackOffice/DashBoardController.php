<?php

namespace App\Controller\BackOffice;

use App\Constant\ReservationType;
use App\Constant\UserConstant;
use App\Entity\Reservation;
use App\Repository\ArticleRepository;
use App\Repository\MissionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/admin', name: 'admin_dash_board_')]
class DashBoardController extends AbstractController
{
    /*
     * A changer le path une fois qu'on a supprimer easy admin
     */
    #[Route('/dashbord', name: 'index')]
    public function index(
        Request $request,
        MissionRepository $missionRepository, 
        UserRepository $userRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ): Response
    {
        $missions = $missionRepository->findBy(['published' => true, 'archived' => false], ['started' => 'ASC']);

        $availables = [];
        $allMissions = [];
        foreach ($missions as $mission) {

            if ($mission->getStarted() <= (new \DateTime('now', new \DateTimeZone('Europe/Paris')))) {
                continue;
            }

            $allMissions[] = $mission;

            if (!$mission->isBooked()) {
                $availables[] = $mission;
            }
        }

        $freelanceNotActive = $userRepository->findByRole(UserConstant::ROLE_FREELANCE);
        $freelanceEnabled = $userRepository->findByRole(UserConstant::ROLE_FREELANCE, true);


        $clientNotEnabled = $userRepository->findByRole(UserConstant::ROLE_CLIENT);
        $clientEnabled = $userRepository->findByRole(UserConstant::ROLE_CLIENT, true);

        $articles = $articleRepository->findBy(['archived' => false, 'published' => true], ['id' => 'DESC']);

        $reservations = $entityManager->getRepository(Reservation::class)->getAffectedReservations($this->getUser());

        $isReserved = false;

        if ($request->get('reservation')) {
            $isReserved = true;
            foreach ($reservations as $reservation) {
                if ($reservation->getId() == $request->get('reservation')) {
                    $isReserved = false;
                }
            }
        }

        if ($isReserved) {
            $this->addFlash('danger', "Hélas ! Cette mission est déjà prise, vous arrivez un peu trop tard. Mais ne vous inquiétez pas, d'autres opportunités arrivent bientôt.");
        }

        return $this->render('back_office/dash_board/index.html.twig', [
            'total' => count($allMissions),
            'availables' => count($availables),
            'freelance_not_enabled' =>  $freelanceNotActive,
            'freelance_enabled' => $freelanceEnabled,
            'client_not_enabled' =>  $clientNotEnabled,
            'client_enabled' => $clientEnabled,
            'last_missions' => count($availables) > 20 ? array_slice($availables, 0, 20) : $availables,
            'articles' => count($articles) > 20 ? array_slice($articles, 0, 20) : $articles,
            'photo_directory' => $this->getParameter('app.relative_path.image_directory'),
            'reservations' => $reservations
        ]);
    }
}
