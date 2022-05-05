<?php

namespace App\Services;

use App\Models\Visita;
use App\Models\VisitaMongo;
use App\Models\VisitaTeste;
use App\Solutions\Util\Util;
use Illuminate\Support\Facades\DB;

class PainelService
{
    public static function getDados($data_de = false, $data_ate = false)
    {
        $dados = VisitaMongo::select('chave_sessao', 'ip', 'data', 'url', 'dispositivo', 'referrer');

        if ($data_de && $data_ate) {
            $dados->whereBetween('data', [Util::formatDataParaMongo($data_de), Util::formatDataParaMongo($data_ate, true)] );
        }

        $dados->groupBy('chave_sessao');

        return $dados;
    }
}