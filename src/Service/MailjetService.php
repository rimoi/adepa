<?php

namespace App\Service;

use Mailjet\Resources;
use Mailjet\Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailjetService
{

    private $params;
    private string $mailerSender;

    public function __construct(ParameterBagInterface $params, string $mailerSender)
    {
        $this->params = $params;
        $this->mailerSender = $mailerSender;
    }

    public function sendEmail(string $email, string $name, string $sujet, string $contenu): void
    {
        $secret = $this->params->get('app.mailjet.secret');
        $key = $this->params->get('app.mailjet.key');
        $mj = new Client(
            $key,
            $secret,
            true,
            ['version' => 'v3.1']
        );

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->mailerSender,
                        'Name' => "ADEPA"
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $name
                        ]
                    ],
                    'Subject' => $sujet,
                    'HTMLPart' => $contenu,
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}