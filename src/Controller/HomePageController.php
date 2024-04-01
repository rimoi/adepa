<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Educatheure;
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

        $educatheurs = $entityManager->getRepository(Educatheure::class)->findBy(['archived' => false, 'published' => true], ['id' => 'DESC'], 6);

        return $this->renderForm('homepage/homepage.html.twig', [
            'articles' => $articles,
            'form' => $form,
            'educatheurs' => $educatheurs,
            'photo_directory' => $this->getParameter('app.relative_path.image_directory'),
        ]);
    }
}