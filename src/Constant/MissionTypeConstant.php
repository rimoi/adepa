<?php

namespace App\Constant;

class MissionTypeConstant
{
    public const HANDICAP_MOTEUR_LABEL = "handicap moteur";
    public const HANDICAP_PSYCHIQUE_LABEL = "handicap psychique";
    public const ADOLESCENT_LABEL = "adolescent";
    public const MIGRANT_LABEL = "migrant";
    public const PERSONNE_AGEE_LABEL = "personne âgée";
    public const AUTRES_LABEL = "autres";

    public const HANDICAP_MOTEUR = "HANDICAP_MOTEUR";
    public const HANDICAP_PSYCHIQUE = "HANDICAP_PSYCHIQUE";
    public const ADOLESCENT = "ADOLESCENT";
    public const MIGRANT = "MIGRANT";
    public const PERSONNE_AGEE = "PERSONNE_AGEE";
    public const AUTRES = "AUTRES";

    public const MAP = [
        self::HANDICAP_MOTEUR => self::HANDICAP_MOTEUR_LABEL ,
        self::HANDICAP_PSYCHIQUE => self::HANDICAP_PSYCHIQUE_LABEL ,
        self::ADOLESCENT => self::ADOLESCENT_LABEL ,
        self::MIGRANT => self::MIGRANT_LABEL ,
        self::PERSONNE_AGEE => self::PERSONNE_AGEE_LABEL ,
        self::AUTRES => self::AUTRES_LABEL ,
    ];
}