<?php

namespace App\Indexation;

class MissionIndexation extends AbstractMeiliSearch
{
    public function getNameIndex(): string
    {
        return 'mission';
    }

    public function addIndexes(array $missions = [])
    {
        $index = $this->meiliSearchClient->index($this->getNameIndex());

        $documents = [];
        foreach ($missions as $mission) {
            $documents[] = $this->serializer->normalize($mission, 'json', ['groups' => ['search']]);
        }

        $index->addDocuments($documents);
    }
}