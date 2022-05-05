<?php
    
namespace App\Repositories;
use App\Solutions\Util\Util;

class PainelRepository extends RepositoryAbstractNoSql 
{
    public function getDadosPainel($data_de, $data_ate){
        
        $dados = $this->model::select('chave_sessao', 'ip', 'data', 'url', 'dispositivo', 'referrer');

        if ($data_de && $data_ate) {
            $dados->whereBetween('data', [Util::formatDataParaMongo($data_de), Util::formatDataParaMongo($data_ate, true)] );
        }
        
        $dados->groupBy('chave_sessao');
        
        return $dados;
    }

}

