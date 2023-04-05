<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    #[Route('/politics', name: 'app_private_politics')]
    public function privatePolitics(): Response
    {
        return $this->render('about/private_politics.html.twig');
    }
}
