<?php

namespace App\Controller\BackOffice;

use App\Entity\Educatheure;
use App\Form\EducatheureType;
use App\Repository\EducatheureRepository;
use App\Service\QualificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/educatheure', name: 'admin_educatheure_')]
class EducatheureController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EducatheureRepository $educatheureRepository): Response
    {
        return $this->render('back_office/educatheure/index.html.twig', [
            'educatheures' => $educatheureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EducatheureRepository $educatheureRepository, QualificationService $qualificationService): Response
    {
        $educatheure = new Educatheure();
        $form = $this->createForm(EducatheureType::class, $educatheure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($days = $form->get('days')->getData()) {
                $educatheure->setDays($days);
            }

            $qualificationService->addElement($form, 'image');

            $educatheure->setUser($this->getUser());
            
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
    public function edit(Request $request, Educatheure $educatheure, EducatheureRepository $educatheureRepository, QualificationService $qualificationService): Response
    {
        $form = $this->createForm(EducatheureType::class, $educatheure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($days = $form->get('days')->getData()) {
                $educatheure->setDays($days);
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
            $educatheureRepository->remove($educatheure, true);
        }

        return $this->redirectToRoute('admin_educatheure_index', [], Response::HTTP_SEE_OTHER);
    }
}
