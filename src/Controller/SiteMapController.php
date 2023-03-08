<?php

namespace App\Controller;

use App\Entity\Mission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SiteMapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_site_map', defaults: ['_format' => 'xml'])]
    public function index(Request $request, UrlGeneratorInterface $urlGenerator, EntityManagerInterface $entityManager): Response
    {

        $urls = [];

        $urls[] = ['loc' => $urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)];
        $urls[] = ['loc' => $urlGenerator->generate('front_mission', [], UrlGeneratorInterface::ABSOLUTE_URL)];
        $urls[] = ['loc' => $urlGenerator->generate('app_contact', [], UrlGeneratorInterface::ABSOLUTE_URL)];
        $urls[] = ['loc' => $urlGenerator->generate('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL)];
        $urls[] = ['loc' => $urlGenerator->generate('app_register', [], UrlGeneratorInterface::ABSOLUTE_URL)];

        $missions = $entityManager->getRepository(Mission::class)->findBy(['archived' => false, 'booked' => false]);

        foreach ($missions as $mission) {
            $urls[] = [
                'loc' => $urlGenerator->generate(
                'front_mission_show',
                ['slug' => $mission->getSlug()],
                UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => $mission->getUpdatedAt() ? $mission->getUpdatedAt()->format('Y-m-d') : $mission->getCreatedAt()->format('Y-m-d'),
                'changefreq' => 'daily',
            ];
        }

        $articles = $entityManager->getRepository(Mission::class)->findBy(['archived' => false, 'published' => true]);

        foreach ($articles as $article) {
            $urls[] = [
                'loc' => $urlGenerator->generate(
                    'app_article',
                    ['slug' => $article->getSlug()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'lastmod' => $article->getUpdatedAt() ? $article->getUpdatedAt()->format('Y-m-d') : $article->getCreatedAt()->format('Y-m-d'),
                'changefreq' => 'monthly',
            ];
        }

        $response = new Response(
             $this->renderView('site_map/index.html.twig', compact('urls')),
            200
        );

        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
