<?php

namespace App\Constant;

class UserConstant
{
    public const ADMIN          = "Administrateur";
    public const FREELANCE      = "Freelance";
    public const CLIENT         = "Client";
    public const DIRECTION      = "Direction d'organisation";
    public const EMPLOYE        = "Salarié";
    public const MANAGER        = "Responsable d'unité";

    public const ROLE_ADMIN     = "ROLE_ADMIN";
    public const ROLE_FREELANCE = "ROLE_FREELANCE";
    public const ROLE_CLIENT    = "ROLE_CLIENT";
    public const ROLE_DIRECTION = "ROLE_DIRECTION";
    public const ROLE_EMPLOYE   = "ROLE_EMPLOYE";
    public const ROLE_MANAGER   = "ROLE_MANAGER";
    

    private static $MAP = [
        self::ADMIN   => self::ADMIN,
        self::FREELANCE => self::FREELANCE,
        self::CLIENT    => self::CLIENT,
        self::MANAGER   => self::MANAGER,
        self::DIRECTION => self::DIRECTION,
        self::EMPLOYE    => self::EMPLOYE
    ];

    private static $MAP_STRING = [
        self::ADMIN   => self::ROLE_ADMIN,
        self::FREELANCE => self::ROLE_FREELANCE,
        self::CLIENT    => self::ROLE_CLIENT,
        self::MANAGER   => self::ROLE_MANAGER,
        self::DIRECTION => self::ROLE_DIRECTION,
        self::EMPLOYE    => self::ROLE_EMPLOYE
    ];

    public static function all(): array
    {
        return self::$MAP;
    }

    public static function asString($role): ?string
    {
        return self::$MAP_STRING[$role] ?? null;
    }

    public static function asStringInverse($role): ?string
    {
        $flipRole = array_flip(self::$MAP_STRING);

        return $flipRole[$role] ?? null;
    }
}