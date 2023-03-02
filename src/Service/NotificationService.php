<?php

namespace App\Service;


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

    public function sendEmail(User $user): void
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

        (new TemplatedEmail())
            ->from(new Address($this->mailerSender, 'ADEPA'))
            ->to($user->getEmail())
            ->subject('ADEPA - Nouvelle candidature')
            ->htmlTemplate($template)
            ->context([
                'user' => $user,
                'homepage' => $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ]);


        $email->setSend(true);
        $this->entityManager->flush();
    }
}