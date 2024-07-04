<?php

namespace App\Controller;

use App\Constant\UserConstant;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AskBookingController extends AbstractController
{
    #[Route('/demande-reservation', name: 'app_demande_reservation', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();

        $session->set('_security.main.target_path', $request->get('url'));

        return $this->redirectToRoute('app_login');
    }
}
