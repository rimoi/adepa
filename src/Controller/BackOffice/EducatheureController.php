<?php

namespace App\Controller\BackOffice;

use App\Constant\UserConstant;
use App\Entity\Educatheure;
use App\Entity\EducatheureTag;
use App\Form\EducatheureType;
use App\Repository\EducatheureRepository;
use App\Service\QualificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/educatheure', name: 'admin_educatheure_')]
class EducatheureController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EducatheureRepository $educatheureRepository): Response
    {
        if ($this->isGranted(UserConstant::ROLE_ADMIN)) {
            $educatheures = $educatheureRepository->findBy(['archived' => false], ['id' => 'DESC']);
        } else {
            $educatheures = $educatheureRepository->getEducatheureByUser($this->getUser());
        }

        return $this->render('back_office/educatheure/index.html.twig', [
            'educatheures' => $educatheures,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EducatheureRepository $educatheureRepository,
        QualificationService $qualificationService,
        EntityManagerInterface $entityManager
    ): Response
    {
        $educatheure = new Educatheure();
        $form = $this->createForm(EducatheureType::class, $educatheure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            if ($dateDebut = $form->get('debut')->getData()) {
//                $dateDebut = str_replace('/', '-', $dateDebut);
//                $educatheure->setStarted(new \DateTimeImmutable($dateDebut, new \DateTimeZone('Europe/Paris')));
//            }
//            if ($dateFin = $form->get('fin')->getData()) {
//                $dateFin = str_replace('/', '-', $dateFin);
//                $educatheure->setEnded(new \DateTimeImmutable($dateFin, new \DateTimeZone('Europe/Paris')));
//            }
//            if ($educatheure->getEnded() <= $educatheure->getStarted()) {
//                $form->get('fin')->addError(new FormError('La date de fin ne peux pas être avant la date de début !'));
//
//                return $this->renderForm('back_office/educatheure/new.html.twig', [
//                    'educatheure' => $educatheure,
//                    'form' => $form,
//                ]);
//            }

            if ($categories = $form->get('publicType')->getData()) {
                foreach ($educatheure->getEducatheureTags() as $educatheureTag) {
                    $entityManager->remove($educatheureTag);
                }

                foreach ($categories as $category) {
                    $categoryTag = new EducatheureTag();
                    $categoryTag->setCategory($category);
                    $categoryTag->setEducatheur($educatheure);

                    $entityManager->persist($categoryTag);
                }
            }


            $qualificationService->addElement($form, 'image');

            if (!$form->has('users')) {
                $educatheure->addUser($this->getUser());
            }
            
            $educatheureRepository->save($educatheure, true);

            $this->addFlash('success', sprintf('Création du service `%s` à bien été effectuée !', $educatheure->getTitle()));

            return $this->redirectToRoute('admin_educatheure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_office/educatheure/new.html.twig', [
            'educatheure' => $educatheure,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show(Educatheure $educatheure): Response
    {
        return $this->render('back_office/educatheure/show.html.twig', [
            'educatheure' => $educatheure,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Educatheure $educatheure,
        EducatheureRepository $educatheureRepository,
        QualificationService $qualificationService,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(EducatheureType::class, $educatheure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

//            if ($dateDebut = $form->get('debut')->getData()) {
//                $dateDebut = str_replace('/', '-', $dateDebut);
//                $educatheure->setStarted(new \DateTimeImmutable($dateDebut, new \DateTimeZone('Europe/Paris')));
//            }
//            if ($dateFin = $form->get('fin')->getData()) {
//                $dateFin = str_replace('/', '-', $dateFin);
//                $educatheure->setEnded(new \DateTimeImmutable($dateFin, new \DateTimeZone('Europe/Paris')));
//            }
//            if ($educatheure->getEnded() <= $educatheure->getStarted()) {
//                $form->get('fin')->addError(new FormError('La date de fin ne peux pas être avant la date de début !'));
//
//                return $this->renderForm('back_office/educatheure/edit.html.twig', [
//                    'educatheure' => $educatheure,
//                    'form' => $form,
//                ]);
//            }

            if ($categories = $form->get('publicType')->getData()) {
                foreach ($educatheure->getEducatheureTags() as $educatheureTag) {
                    $entityManager->remove($educatheureTag);
                }

                foreach ($categories as $category) {
                    $categoryTag = new EducatheureTag();
                    $categoryTag->setCategory($category);
                    $categoryTag->setEducatheur($educatheure);

                    $entityManager->persist($categoryTag);
                }
            }

            if (!$form->has('users')) {
                $educatheure->addUser($this->getUser());
            }

            $qualificationService->addElement($form, 'image');

            $educatheureRepository->save($educatheure, true);

            $this->addFlash('success', sprintf('Modification du service `%s` à bien été effectuée !', $educatheure->getTitle()));


            return $this->redirectToRoute('admin_educatheure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_office/educatheure/edit.html.twig', [
            'educatheure' => $educatheure,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/deleted', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Educatheure $educatheure, EducatheureRepository $educatheureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$educatheure->getId(), $request->request->get('_token'))) {

            $educatheure->setArchived(!$educatheure->isArchived());

            $educatheureRepository->save($educatheure, true);

            $this->addFlash('success', sprintf('Service `%s` à bien été archivé !', $educatheure->getTitle()));
        }

        return $this->redirectToRoute('admin_educatheure_index', [], Response::HTTP_SEE_OTHER);
    }
}
