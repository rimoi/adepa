<?php

namespace App\Constant;

class ReservationType
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const TERMINATE = 'terminate';


    public const MAP  = [
      self::PENDING => 'En attente',
      self::CONFIRMED => 'Confirmé',
      self::TERMINATE => 'Terminé',
    ];
}