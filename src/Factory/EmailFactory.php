<?php

namespace App\Factory;

use App\Entity\Email;
use App\Entity\User;

class EmailFactory
{
    public static function create(array $options =  []): Email
    {
        $email = new Email();

        if ($options['user'] ?? false) {
            /** @var User $user */
            $user = $options['user'];
            $email->setReceiver($user->getEmail());
            $email->setUserId($user->getId());
        }

        if ($options['sender'] ?? false) {
            $email->setSender($options['sender']);
        }

        if ($options['template'] ?? false) {
            $email->setTemplate($options['template']);
        }

        return $email;
    }
}