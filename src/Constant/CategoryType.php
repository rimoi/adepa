<?php

namespace App\Constant;

class CategoryType
{
    public const MISSION = 'Mission';
    public const SERVICE = 'Service';
    public const PUBLIC = 'Public';

    public const MAP = [
        self::MISSION => self::MISSION,
        self::SERVICE => self::SERVICE,
        self::PUBLIC => self::PUBLIC,
    ];
}