<?php

namespace App\Twig\Extension;

use App\Entity\NewRequest;
use App\Twig\Runtime\EducatheurExtensionRuntime;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EducatheurExtension extends AbstractExtension
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    { }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [EducatheurExtensionRuntime::class, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dontHaveRequest', [$this, 'dontHaveRequestExtension']),
        ];
    }

    public function dontHaveRequestExtension(int $educatheurId, int $userId): bool
    {
        return !!! $this->entityManager->getRepository(NewRequest::class)->findOneBy(['educatheur' => $educatheurId, 'user' => $userId]);
    }
}
