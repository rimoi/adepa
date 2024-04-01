<?php

namespace App\Constant;

class PublicType
{
    public const ADOLESCENT = 'adolescent';
    public const CHILD = 'child';
    public const DISABILITY = 'disability';
    public const MINOR = 'minor';
    public const SENIOR = 'senior';

    public const MAP  = [
      'Adolescent' => self::ADOLESCENT,
      'Enfant' => self::CHILD,
      'Handicapé' => self::DISABILITY,
      'Mineur non accompagné' => self::MINOR,
      'Sénior' => self::SENIOR,
    ];

    public const REVERSE_MAP = [
        self::ADOLESCENT => 'Adolescent',
        self::CHILD => 'Enfant',
        self::DISABILITY => 'Handicapé',
        self::MINOR => 'Mineur non accompagné',
        self::SENIOR => 'Sénior',
    ];
}