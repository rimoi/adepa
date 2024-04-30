<?php

namespace App\Constant;

class ReservationType
{
    public const CREATED = 'created';
    public const ACCEPTED = 'accepted';
    public const REFUSED = 'refused';
    public const TERMINATE = 'terminate';


    public const MAP  = [
      self::CREATED => 'Crée',
      self::ACCEPTED => 'Accepté',
      self::REFUSED => 'Refusé',
      self::TERMINATE => 'Terminé',
    ];
}