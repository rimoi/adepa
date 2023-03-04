<?php

namespace App\Service;


use App\Constant\NotificationConstant;
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

    public function __construct(
        MailerInterface $mailer,
        string $mailerSender,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->mailer = $mailer;
        $this->mailerSender = $mailerSender;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }

    public function infoUserMission(Mission $mission, string $type = NotificationConstant::EMAIL): void
    {
        $categories = $mission->getCategories();

        $parents = [];

        foreach ($categories as $category) {
            $parents[$category->getParent()->getId()] = $category->getParent()->getId();
        }
        $users = $this->entityManager->getRepository(User::class)->findByCategory($parents);

        foreach ($users as $user) {
            $this->sendEmailToFreelanceSuggestMission($user, $mission);
        }
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

    // accusé de reception de candidature
    public function sendEmailToFreelanceCancel(User $user, Mission $mission): void
    {
        $template = 'mailing/acknowledge_cancel_candidate.html.twig';

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
            ->subject('ADEPA - Annulation de mission : ' . $mission->getTitle())
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

    public function sendEmailToClientCancel(User $user, Mission $mission): void
    {
        $template = 'mailing/candidate_cancel.html.twig';

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
            ->subject('ADEPA - Annulation pour le poste : ' . $mission->getTitle())
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
}