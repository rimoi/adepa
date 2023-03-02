<?php

namespace App\Constant;

class MissionTypeConstant
{
    public const HEBERGEMENT = "Hébergement";
    public const HOPITAL = "Hôpital de service";

    public const MAP = [
        self::HEBERGEMENT => self::HEBERGEMENT,
        self::HOPITAL => self::HOPITAL,
    ];
}