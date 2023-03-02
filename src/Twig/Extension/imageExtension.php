<?php

namespace App\Twig\Extension;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class imageExtension extends AbstractExtension
{

    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('image64', [$this, 'image64']),
        ];
    }

    public function image64(string $imagePath)
    {
        return (string) $this->normalizer->normalize(new \SplFileObject($imagePath));
    }
}
