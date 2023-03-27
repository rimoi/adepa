<?php

namespace App\Twig\Components;

use App\Service\SearchService;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('search_mission')]
class SearchMission
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(
        private SearchService $searchService
    ) {
    }

    /**
     * @return Mission[]
     */
    public function getMissions(): array
    {
        return $this->searchService->searchByTerm($this->query);
    }
}