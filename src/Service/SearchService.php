<?php

namespace App\Service;

use App\Entity\Mission;
use App\helper\ArrayHelper;
use App\Indexation\MissionIndexation;
use Doctrine\ORM\EntityManagerInterface;

class SearchService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MissionIndexation $missionIndexation
    ) {
    }

    public function searchByTerm(string $term): array
    {
        // Response de ce type
        //  Ex:
        //  $response = [
        //      0 => [
        //         'id' => 1252,
        //         'title' => 'Développeur web',
        //         'content' => 'lorem ipsum ....',
        //      ]
        //  ];
        //
        //
        $response = $this->missionIndexation->search(
            $this->getOptionsSearch($term)
        );


        // Response de ce type
        //  Ex:
        //  return = [
        //      0 => App\Entity\Mission {#522 ▶}
        //      1 => App\Entity\Mission {#652 ▶}
        //  ];
        //
        //
        return $this->convertToObject($response);
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

    private function convertToObject(array $data): array
    {
        $result = ArrayHelper::columnize($data, '[id]');

        $missions = $this->entityManager->getRepository(Mission::class)->findBy(['id' => $result]);

        foreach ($missions as $key => $mission) {
            $mission->setContent($data[$key]['_formatted']['content'] ?? $mission->getContent());
            $mission->setTitle($data[$key]['_formatted']['title'] ?? $mission->getTitle());
        }

        return $missions;
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
}