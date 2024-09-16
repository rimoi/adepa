<?php

namespace App\Twig\Components;

use App\Entity\Mission;
use App\Repository\MissionRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('mission')]
class MissionComponent
{
    public int $id;

    public function __construct(
        private MissionRepository $missionRepository
    ) {
    }

    public function mission(): Mission
    {
        return $this->missionRepository->find($this->id);
    }
}