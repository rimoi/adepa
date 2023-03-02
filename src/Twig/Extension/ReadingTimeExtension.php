<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ReadingTimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('readtime', [$this, 'readtime']),
        ];
    }

    public function readtime($content)
    {
        $words = str_word_count(strip_tags($content));
        $min = floor($words / 200);
        $return = ($min < 1 ? '1' : $min);

        return $return;
    }
}
