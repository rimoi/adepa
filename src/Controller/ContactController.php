<?php

namespace App\Controller;

use App\Constant\UserConstant;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, ContactRepository $contactRepository, NotificationService $notificationService): Response
    {
        $contact =  new Contact();

        if ($this->getUser()) {
            $contact->setEmail($this->getUser()->getEmail());
            $contact->setPhone($this->getUser()->getTelephone());
            $contact->setName($this->getUser()->nickname());

            $contact->setType(
                $this->getUser()->hasRole(UserConstant::ROLE_FREELANCE) ? UserConstant::ROLE_FREELANCE : UserConstant::ROLE_CLIENT
            );
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success', 'Votre email a bien été envoyé !');

            $contactRepository->save($contact, true);

            $notificationService->sendEmailWhenContactAdmin($contact);

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
