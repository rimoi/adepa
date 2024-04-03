<?php

namespace App\Constant;

class Days
{
    public const Lundi = 'Lun';
    public const MARDI = 'Mar';
    public const MERCREDI = 'Mer';
    public const JEUDI = 'Jeu';
    public const VENDREDI = 'Ven';
    public const SAMEDI = 'Sam';
    public const DIMANCHE = 'Dim';

    public const MAP = [
        self::Lundi => self::Lundi,
        self::MARDI => self::MARDI,
        self::MERCREDI => self::MERCREDI,
        self::JEUDI => self::JEUDI,
        self::VENDREDI => self::VENDREDI,
        self::SAMEDI => self::SAMEDI,
        self::DIMANCHE => self::DIMANCHE,
    ];

    public const MONTH = [
        'January' => 'Janvier',
        'February' => 'Février',
        'March' => 'Mars',
        'April' => 'Avril',
        'May' => 'Mai',
        'June' => 'Juin',
        'July' => 'Juillet',
        'August' => 'Août',
        'September' => 'Septembre',
        'October' => 'Octobre',
        'November' => 'Novembre',
        'December' => 'Décembre'
    ];
}