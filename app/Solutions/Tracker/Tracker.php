<?php

namespace App\Solutions\Tracker;

use App\Services\TrackerService;
use App\Solutions\Util\Util;
use PhpParser\Node\Stmt\Foreach_;

class Tracker
{
    public static function geraTag()
    {
        $tag = '';
        $letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P' ,'Q', 'R', 'S', 'T', 'U', 'V', 'X', 'Z'];
        
        
        $tag .= $letras[rand(0, 23)];
        $tag .= $letras[rand(0, 23)];

        $tag .= rand(100000, 999999);

        return $tag;
    }
}
