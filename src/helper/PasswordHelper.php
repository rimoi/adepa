<?php

namespace App\helper;

use Symfony\Component\Form\FormInterface;

class PasswordHelper
{

    public static function generate(array $options): string
    {
        $lowerchars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $upperchars = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $symbols = ['~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '+', '`', '-', '=', '{', '}', '[', ']', ':', ';', '<', '>', ',', '.', '?', '/'];
        //$symbolsCommon = ['!', '@', '#', '$', '%', '&', '*', '(', ')', '+', '-', '{', '}', '[', ']', '<', '>', '.', '?'];

        $source = [];
        $source = array_merge($source, $lowerchars);
        $source = array_merge($source, $upperchars);
        $source = array_merge($source, $digits);
        
        //$source = array_merge($source, $symbolsCommon);
        $source = array_merge($source, $symbols);
        
        
        $length = rand($options['min'], $options['max']);
        $word = '';
        for ($i = 0; $i < $length; $i ++) {
            $word .= $source[array_rand($source)];
        }
        
        return $word;
    }

}