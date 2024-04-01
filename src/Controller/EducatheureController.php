<?php

namespace App\Controller;

use App\Constant\PublicType;
use App\Entity\Educatheure;
use App\helper\ArrayHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/educatheure', name: 'app_educatheure_')]
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
}
