<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class VisitaTeste extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'visitas_teste';
    protected $dates = ['data'];
    public $timestamps = false;

    protected $fillable = [
        'tag',
        'ip',
        'data',
        'url',
        'chave_sessao',
        'dispositivo',
        'pais',
        'cidade',
        'estadoc',
        'origem',
        'referrer',
        'resolucao',
        'os',
        "host",
        "path",
        "search",
        'midia',
        'origem',
        'paginas'
    ];
    
}
