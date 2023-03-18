<?php

namespace App\Constant;

class GenderConstant
{
    public const MONSIEUR     = "M.";
    public const MADAME = "Mme.";
    public const NON_PRECISE      = "Non précisé";

    public const MAP = [
        self::MONSIEUR => self::MONSIEUR,
        self::MADAME => self::MADAME,
        self::NON_PRECISE => self::NON_PRECISE,
    ];
}