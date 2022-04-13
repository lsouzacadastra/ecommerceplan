<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visitas';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'ip', 
        'data',
        'hora',
        'url',
        'chave_sessao',
        'dispositivo',
        'pais',
        'paisc',
        'estado',
        'estadoc',
        'cidade',
        'origem',
        'referrer',
        'resolucao',
        'os'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function done()
    {
        $this->update([

        ]);
    }
}
