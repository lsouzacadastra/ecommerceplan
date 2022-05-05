<?php

namespace App\Repositories;

use App\Models\Visita;

class RepositoryAbstractSql
{
    protected $model;

    protected function resolveModel(){
        $this->model = $this->resolveModel();
    }

    protected function __construct()
    {
        return app($this->model);
    }
}