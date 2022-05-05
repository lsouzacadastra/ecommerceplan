<?php

namespace App\Solutions\Tracker;

class Tracker
{
    public static function geraTag()
    {
        $tag = '';
        $letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P' ,'Q', 'R', 'S', 'T', 'U', 'V', 'X', 'Z'];
        
        $tag .= $letras[rand(0, 23)];
        $tag .= $letras[rand(0, 23)];

        $tag .= rand(100000, 999999);
        $tag .= $letras[rand(0, 23)];
        $tag .= $letras[rand(0, 23)];

        return $tag;
    }
}
