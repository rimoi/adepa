<?php

namespace App\Service;

use App\Entity\User;
use App\Factory\SmsFactory;
use App\helper\PhoneHelper;
use Doctrine\ORM\EntityManagerInterface;
use MessageBird\Exceptions\MessageBirdException;
use MessageBird\Objects\Message;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use MessageBird\Client;
use MessageBird\Exceptions\AuthenticateException;
use MessageBird\Exceptions\BalanceException;
use Twig\Environment;

class MessageBird
{

    private $entityManager;
    private string $tokenMessageBird;
    private ParameterBagInterface $parameterBag;
    private Environment $twig;

    public function __construct(
        string $tokenMessageBird,
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $entityManager,
        Environment $twig
    )
    {
        $this->entityManager = $entityManager;
        $this->tokenMessageBird = $tokenMessageBird;
        $this->parameterBag = $parameterBag;
        $this->twig = $twig;
    }

    public function sendSMS(User $user, string $template, array $params = []): bool
    {
        $options = [
            'user' => $user,
            'content' => $this->twig->render($template, $params)
        ];

        $sms = SmsFactory::create($options);

        $this->entityManager->persist($sms);

        $client = new Client($this->tokenMessageBird);

        $message = new Message();
        $message->originator = $sms->getSender();
        $message->recipients = [ PhoneHelper::cleanNumber($sms->getPhone()) ];
//
//        if ($this->parameterBag->get('kernel.environment') === 'dev') {
//            $message->recipients = [ '33605992481' ];
//        }

        $message->body = $sms->getContent();

        try {
            $client->messages->create($message);

            $sms->setSend(true);

            $this->entityManager->flush();

            return true;

        } catch (AuthenticateException $e) {
            // That means that your accessKey is unknown
            echo 'wrong login';
        } catch (MessageBirdException $e) {
            echo $e->getMessage();
        } catch (BalanceException $e) {
            // That means that you are out of credits, so do something about it.
            echo 'no balance';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return false;
    }
}