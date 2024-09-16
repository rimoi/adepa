<?php

namespace App\Indexation;

use App\Entity\IndexSearchInterface;
use App\Entity\Mission;
use Doctrine\ORM\EntityManagerInterface;
use Meilisearch\Http\Client as MeiliSearchClient;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractMeiliSearch implements IndexationInterface
{
    abstract protected function getNameIndex(): ?string;

    protected MeiliSearchClient $meiliSearchClient;

    public function __construct(
        string $meiliSearchHost,
        string $meiliSearchKey,
        protected SerializerInterface $serializer,
        protected EntityManagerInterface $em
    )
    {
        $this->meiliSearchClient = new MeiliSearchClient($meiliSearchHost, $meiliSearchKey);
    }


    /**
     * @param IndexSearchInterface|Mission $mission
     */
    public function create(IndexSearchInterface $mission): bool
    {
        $document = $this->serializer->normalize($mission, 'json', ['groups' => ['search']]);

        $path = sprintf('/indexes/%s/documents', $this->getNameIndex());

        $result = $this->meiliSearchClient->post($path, [$document]);

        if ($result['taskUid'] ?? false) {
            $mission->setTasks($result['taskUid']);
            $this->em->flush();
        }

        return $result['taskUid'] ?? false;
    }

    /**
     * @param IndexSearchInterface|Mission $mission
     */
    public function update(IndexSearchInterface $mission): bool
    {
        $document = $this->serializer->normalize($mission, 'json', ['groups' => ['search']]);

        $path = sprintf('/indexes/%s/documents', $this->getNameIndex());

        $result = $this->meiliSearchClient->put($path, [$document]);

        if ($result['taskUid'] ?? false) {
            $mission->setTasks($result['taskUid']);
            $this->em->flush();
        }

        return $result['taskUid'] ?? false;
    }

    public function delete(IndexSearchInterface $mission): bool
    {
        $path = sprintf('/indexes/%s/documents/%s', $this->getNameIndex(), $mission->getId());

        $result = $this->meiliSearchClient->delete($path);

        $mission->setTasks(null);
        $this->em->flush();

        return $result['taskUid'] ?? false;
    }

    public function search(array $options = [])
    {
        $path = sprintf('/indexes/%s/search', $this->getNameIndex());

        $response = $this->meiliSearchClient->post($path, $options);

        return $response['hits'] ?? [];
    }
}