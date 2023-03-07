<?php

namespace App\Factory;

use App\Entity\Sms;
use App\Entity\User;

class SmsFactory
{
    public static function create(array $options =  []): Sms
    {
        $sms = new Sms();

        if ($options['user'] ?? false) {
            /** @var User $user */
            $user = $options['user'];
            $sms->setPhone($user->getTelephone());
            $sms->setUser($user);
        }

        if ($options['content'] ?? false) {
            $sms->setContent($options['content']);
        }

        return $sms;
    }
}