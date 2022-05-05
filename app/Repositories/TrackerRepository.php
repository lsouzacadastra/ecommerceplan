<?php
    
namespace App\Repositories;
use App\Models\VisitaTeste;

class TrackerRepository
{
    public function __construct()
    {
        $this->model = VisitaTeste::class;
    }

    public function verificaVisitaExiste($chave_sessao)
    {
        $dados = $this->model::where('chave_sessao', $chave_sessao)->get();
        return $dados;
    }

    public function criaVisita($visita)
    {
        $this->model::create($visita);
    }

    public function atualizaVisita($dados, $paginas_atualizar){
        $this->model::where( array ('chave_sessao' => $dados['k']) )->update([ 'paginas' => $paginas_atualizar], ['upsert' => true] );
    }

}

