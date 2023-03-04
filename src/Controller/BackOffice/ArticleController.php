<?php

namespace App\Controller\BackOffice;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\QualificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/article', name: 'admin_article_')]
#[IsGranted('ROLE_ADMIN')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('back_office/article/index.html.twig', [
            'articles' => $articleRepository->findBy(['archived' => false],  ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository, QualificationService $qualificationService): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setUser($this->getUser());

            $qualificationService->addElement($form, 'file');

            $articleRepository->save($article, true);

            $this->addFlash('success', sprintf('Article `%s` à été crée !', $article->getTitle()));

            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_office/article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('back_office/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository, QualificationService $qualificationService): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $qualificationService->addElement($form, 'file');

            $articleRepository->save($article, true);

            $this->addFlash('success', sprintf('Article `%s` modifié !', $article->getTitle()));

            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_office/article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/archived', name: 'archived', methods: ['POST', 'GET'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $article->setArchived(!$article->isArchived());

        $this->addFlash('success', sprintf('Article `%s` archivé !', $article->getTitle()));

        $entityManager->flush();

        return $this->redirectToRoute('admin_article_index');
    }
}
