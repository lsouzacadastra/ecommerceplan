<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class VisitaMongo extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'visitas';
    protected $dates = ['data'];
    
}
