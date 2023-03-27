<?php

namespace App\Indexation;

class MissionIndexation extends AbstractMeiliSearch
{
    public function getNameIndex(): string
    {
        return 'mission';
    }
}