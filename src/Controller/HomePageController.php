<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(SearchType::class);

        $filter = [
            'published' => true,
            'archived' => false,
        ];

        $articles = $entityManager->getRepository(Article::class)->findBy($filter, ['id' => 'DESC'], 4);

        return $this->renderForm('homepage/homepage.html.twig', [
            'articles' => $articles,
            'form' => $form,
            'photo_directory' => $this->getParameter('app.relative_path.image_directory'),
        ]);
    }
}