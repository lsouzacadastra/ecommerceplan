<?php

namespace App\Services;

use App\Solutions\Util\Util;
use App\Repositories\TrackerRepository;

class TrackerService
{
    public function registraVisista($dados){

        $util = new Util();
        $repository = new TrackerRepository();

        //Verifica se visita existe
        $visita_exist = $repository->verificaVisitaExiste($chave_sessao = $dados['k']);

        //Cria visita
        if(empty(@$visita_exist[0]['chave_sessao'])){
            
            $ip = $dados['ip'];

            //Comunicação com a API que pega localização
            $dados['localizacao'] = (array) json_decode( file_get_contents("http://ip-api.com/json/$ip") );
            
            $midia_origem = $util->trataOrigemMidiaPath($dados['path'], $dados['search'], $dados['referrer']);

            $origem = $midia_origem['origem'];
            $midia = $midia_origem['midia'];
        
            $visita = [
                'tag' => $dados['tag'],
                'ip' => $ip, 
                'data' => date('Y-m-d H:i:s'), 
                'chave_sessao' => $dados['k'],
                'dispositivo' => $dados['dispositivo'],
                
                'pais' => $dados['localizacao']['country'], 
                'paisc' => $dados['localizacao']['countryCode'], 
                'estado' => $dados['localizacao']['regionName'], 
                'estadoc' => $dados['localizacao']['region'],
                'cidade' => $dados['localizacao']['city'],
                'origem' => $origem,
                'midia' => $midia,
                'referrer' => $dados['referrer'],
                'host' => $dados['host'],
                'search' => $dados['search'],
                "paginas" => [
                    [
                    'hora' => date('Y-m-d H:i:s'), 
                    'url' => $dados['path']
                    ]
                ]
            ];

            $repository->criaVisita($visita);
            

        //Atualiza URLs
        }else{

            $paginas = $visita_exist[0]['paginas'];
            $atualizar = true;
            
            foreach($paginas as $v){
                if($v['url'] == $dados['path']){
                    $atualizar = false;
                    break;
                }
            }

            if($atualizar){
                $paginas_atualizar = $visita_exist[0]['paginas'];
                $paginas_atualizar[] = ['hora' => date('Y-m-d H:i:s'), 'url' => $dados['path'] ];

                $repository->atualizaVisita($dados, $paginas_atualizar);
            }
        }

        return json_encode([@$paginas_atualizar, @$visita]);
    }
}
