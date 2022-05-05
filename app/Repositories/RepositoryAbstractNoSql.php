<?php

namespace App\Repositories;

use App\Models\VisitaMongo;

class RepositoryAbstractNoSql
{
    public $model;

    public function __construct(){
        $this->model = VisitaMongo::class;
    }
}