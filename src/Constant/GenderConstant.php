<?php

namespace App\Constant;

class GenderConstant
{
    public const MONSIEUR     = "M.";
    public const MADAME = "Mme.";
    public const MADEMOISELLE      = "Mlle.";

    public const MAP = [
        self::MONSIEUR => self::MONSIEUR,
        self::MADAME => self::MADAME,
        self::MADEMOISELLE => self::MADEMOISELLE,
    ];
}