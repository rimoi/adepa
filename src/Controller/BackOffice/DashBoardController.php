<?php

namespace App\Controller\BackOffice;

use App\Constant\UserConstant;
use App\Repository\ArticleRepository;
use App\Repository\MissionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_dash_board_')]
class DashBoardController extends AbstractController
{
    /*
     * A changer le path une fois qu'on a supprimer easy admin
     */
    #[Route('/dashbord', name: 'index')]
    public function index(
        MissionRepository $missionRepository, 
        UserRepository $userRepository,
        ArticleRepository $articleRepository
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

        $articles = $articleRepository->findBy([], ['id' => 'DESC']);

        return $this->render('back_office/dash_board/index.html.twig', [
            'total' => count($allMissions),
            'availables' => count($availables),
            'freelance_not_enabled' =>  $freelanceNotActive,
            'freelance_enabled' => $freelanceEnabled,
            'client_not_enabled' =>  $clientNotEnabled,
            'client_enabled' => $clientEnabled,
            'last_missions' => array_slice($availables, 0, 20),
            'articles' => array_slice($articles, 0, 20),
            'photo_directory' => $this->getParameter('app.image_directory'),
        ]);
    }
}
