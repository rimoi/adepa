<?php

namespace App\helper;


class PhoneHelper
{
    public static function cleanNumber(string $number): string
    {
        if (strlen($number) === 10) {
            return '33'.substr($number, 1);
        }

        return $number;
    }
}