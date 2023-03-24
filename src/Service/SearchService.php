<?php

namespace App\Service;

use App\Entity\Mission;
use App\helper\ArrayHelper;
use App\Indexation\MissionIndexation;
use Doctrine\ORM\EntityManagerInterface;

class SearchService
{
    private EntityManagerInterface $entityManager;
    private MissionIndexation $missionIndexation;

    public function __construct(
        EntityManagerInterface $entityManager,
        MissionIndexation $missionIndexation
    ) {
        $this->entityManager = $entityManager;
        $this->missionIndexation = $missionIndexation;
    }

    public function search(array $categories, ?string $term): array
    {
        $missions = [];

        if ($categories) {
            $missions = $this->entityManager->getRepository(Mission::class)->getMissionByCriteria($categories);
        }

        if ($term) {
            $result = $this->missionIndexation->search(
                $this->getOptionsSearch($term)
            );

            if ($result) {
                $result = ArrayHelper::createAssociativeArray($result, '[id]');

                if ($missions) {
                    $mission_inter = array_intersect(
                        array_keys($result),
                        ArrayHelper::createAssociativeArray($missions, 'id', 'id')
                    );

                    if ($mission_inter) {
                        $missions = $this->entityManager->getRepository(Mission::class)->findBy(['id' => $mission_inter]);
                        foreach ($missions as $mission) {
                            $mission->setContent($result[$mission->getId()]['_formatted']['content'] ?? $mission->getContent());
                            $mission->setTitle($result[$mission->getId()]['_formatted']['title'] ?? $mission->getTitle());
                        }
                    }
                } else {
                    $missions = $this->entityManager->getRepository(Mission::class)->findBy(['id' => array_keys($result)]);
                    foreach ($missions as $mission) {
                        $mission->setContent($result[$mission->getId()]['_formatted']['content'] ?? $mission->getContent());
                        $mission->setTitle($result[$mission->getId()]['_formatted']['title'] ?? $mission->getTitle());
                    }
                }
            }
        }

        return $missions;
    }

    private function getOptionsSearch(string $term): array
    {
        return [
            'q' => $term,
            'attributesToHighlight' => ['title', 'content'],
            'attributesToCrop' => ['content'],
            'cropLength' => 8,
            'highlightPreTag' => '<span class="cs-highlight">',
            'highlightPostTag' => '</span>'
        ];
    }
}