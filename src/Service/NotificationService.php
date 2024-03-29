<?php

namespace App\Service;


use App\Constant\NotificationConstant;
use App\Entity\Booking;
use App\Entity\Contact;
use App\Entity\Mission;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use App\Factory\EmailFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NotificationService
{
    private MailerInterface $mailer;
    private string $mailerSender;
    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private MessageBird $messageBird;

    public function __construct(
        MailerInterface $mailer,
        string $mailerSender,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        MessageBird $messageBird
    )
    {
        $this->mailer = $mailer;
        $this->mailerSender = $mailerSender;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->messageBird = $messageBird;
    }

    public function infoUserMission(Mission $mission, string $type = NotificationConstant::EMAIL, array $users = []): void
    {
        if (!$users) {
            $categories = $mission->getCategories();

            $parents = [];

            foreach ($categories as $category) {
                $parents[$category->getParent()->getId()] = $category->getParent()->getId();
            }
            $users = $this->entityManager->getRepository(User::class)->findByCategory($parents);
        }

        switch ($type) {
            case NotificationConstant::EMAIL:
                foreach ($users as $user) {
                    $this->sendEmailToFreelanceSuggestMission($user, $mission);
                }
                break;
            case NotificationConstant::SMS:
                foreach ($users as $user) {
                    $this->sendSMSEmergency($user, $mission);
                }
                break;
        }
    }

    private function sendSMSEmergency(User $user, Mission $mission)
    {
        $template = 'sms/admin/mission_available_emergency.txt.twig';
        $params = [
            'url' => $this->urlGenerator->generate('front_mission_show_with_id', ['id' => $mission->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'user' => $user
        ];

        $this->messageBird->sendSMS($user, $template, $params);
    }

    // suggestion de mission
    public function sendEmailToFreelanceSuggestMission(User $user, Mission $mission): void
    {
        $template = 'mailing/suggest_mission.html.twig';

        $options = [
            'user' => $user,
            'sender' => $this->mailerSender,
            'template' => $template,
        ];

        $email = EmailFactory::create($options);

        $this->entityManager->persist($email);
        $this->entityManager->flush();

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to($user->getEmail())
            ->subject('ADEPA - Une nouvelle mission qui match avec votre profil 🎁')
            ->htmlTemplate($template)
            ->context([
                'user' => $user,
                'mission' => $mission,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);


        $email->setSend(true);
        $this->entityManager->flush();
    }

    // confirmation de candidature
    public function sendEmailToClient(User $user, Mission $mission): void
    {
        $template = 'mailing/candidate.html.twig';

        $options = [
            'user' => $user,
            'sender' => $this->mailerSender,
            'template' => $template,
        ];

        $email = EmailFactory::create($options);

        $this->entityManager->persist($email);
        $this->entityManager->flush();

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to($mission->getUser()->getEmail())
            ->subject('ADEPA - Nouvelle candidature pour le poste : ' . $mission->getTitle())
            ->htmlTemplate($template)
            ->context([
                'user' => $user,
                'mission' => $mission,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);


        $email->setSend(true);
        $this->entityManager->flush();
    }

    // accusé de reception de candidature
    public function sendEmailToFreelanceCandidate(User $user, Mission $mission): void
    {
        $template = 'mailing/acknowledge_receipt_candidate.html.twig';

        $options = [
            'user' => $user,
            'sender' => $this->mailerSender,
            'template' => $template,
        ];

        $email = EmailFactory::create($options);

        $this->entityManager->persist($email);
        $this->entityManager->flush();

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to($user->getEmail())
            ->subject('ADEPA - Accusé réception de votre candidature')
            ->htmlTemplate($template)
            ->context([
                'user' => $user,
                'mission' => $mission,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);


        $email->setSend(true);
        $this->entityManager->flush();
    }

    // annulation depuis front ou de l'admin
    public function sendEmailToFreelanceCancel(
        User $user,
        Mission $mission,
        string $template = 'mailing/acknowledge_cancel_candidate.html.twig'
    ): void
    {
        $options = [
            'user' => $user,
            'sender' => $this->mailerSender,
            'template' => $template,
        ];

        $email = EmailFactory::create($options);

        $this->entityManager->persist($email);
        $this->entityManager->flush();

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to($user->getEmail())
            ->subject('ADEPA - Annulation : ' . $mission->getTitle())
            ->htmlTemplate($template)
            ->context([
                'user' => $user,
                'mission' => $mission,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);


        $email->setSend(true);
        $this->entityManager->flush();
    }

    public function sendEmailToClientCancel(
        User $user,
        Mission $mission,
        string $template = 'mailing/candidate_cancel.html.twig'
    ): void
    {
        $options = [
            'user' => $user,
            'sender' => $this->mailerSender,
            'template' => $template,
        ];

        $email = EmailFactory::create($options);

        $this->entityManager->persist($email);
        $this->entityManager->flush();

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to($mission->getUser()->getEmail())
            ->subject('ADEPA - Annulation du poste : ' . $mission->getTitle())
            ->htmlTemplate($template)
            ->context([
                'user' => $user,
                'mission' => $mission,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);


        $email->setSend(true);
        $this->entityManager->flush();
    }

    public function sendEmailWhenContactAdmin(Contact $contact): void
    {
        $template = 'mailing/contact.html.twig';

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to($this->mailerSender)
            ->subject('ADEPA - Prise de contact ')
            ->htmlTemplate($template)
            ->context([
                'contact' => $contact,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);

    }

    public function sendEmailToClientConfirmTime(
        Booking $booking,
        Mission $mission,
        string $template = 'mailing/admin/confirm_date.html.twig'
    ): void
    {
        $options = [
            'user' => $booking->getUser(),
            'sender' => $this->mailerSender,
            'template' => $template,
        ];

        $email = EmailFactory::create($options);

        $this->entityManager->persist($email);
        $this->entityManager->flush();

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to($mission->getUser()->getEmail())
            ->subject('ADEPA - Merci de confirmer les heures de travails pour la mission : ' . $mission->getTitle())
            ->htmlTemplate($template)
            ->context([
                'user' => $booking->getUser(),
                'booking' => $booking,
                'mission' => $mission,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);


        $email->setSend(true);
        $this->entityManager->flush();
    }

    public function sendEmailToClientConfirmFine(
        Booking $booking,
        Mission $mission,
        string $template = 'mailing/admin/confirm_fine.html.twig'
    ): void
    {
        $options = [
            'user' => $booking->getUser(),
            'sender' => $this->mailerSender,
            'template' => $template,
        ];

        $email = EmailFactory::create($options);

        $this->entityManager->persist($email);
        $this->entityManager->flush();

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to('assoc.adepa@gmail.com')
            ->subject('ADEPA - Merci de générer la facture pour la mission : ' . $mission->getTitle())
            ->htmlTemplate($template)
            ->context([
                'user' => $booking->getUser(),
                'booking' => $booking,
                'mission' => $mission,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);


        $email->setSend(true);
        $this->entityManager->flush();
    }


    public function sendEmailAdminConflitValidateTime(
        Booking $booking,
        Mission $mission,
        array $tab
    ): void
    {

        $template = 'mailing/admin/conflit.html.twig';

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to('assoc.adepa@gmail.com')
            ->subject('ADEPA - Conflit entre client et freelance pour les heures de la mission : ' . $mission->getTitle())
            ->htmlTemplate($template)
            ->context([
                'user' => $booking->getUser(),
                'booking' => $booking,
                'mission' => $mission,
                'tab' => $tab,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);

    }

    public function sendEmailToFreelanceConfirmTime(Booking $booking, Mission $mission, string $template)
    {
        $options = [
            'user' => $booking->getUser(),
            'sender' => $this->mailerSender,
            'template' => $template,
        ];

        $email = EmailFactory::create($options);

        $this->entityManager->persist($email);
        $this->entityManager->flush();

        $templateEmail = (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to($booking->getUser()->getEmail())
            ->subject('ADEPA - Merci de confirmer les heures de travails pour la mission : ' . $mission->getTitle())
            ->htmlTemplate($template)
            ->context([
                'user' => $booking->getUser(),
                'booking' => $booking,
                'mission' => $mission,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);

        $this->mailer->send($templateEmail);


        $email->setSend(true);
        $this->entityManager->flush();

    }

}