<?php

namespace App\Services;

use App\Models\Visita;
use App\Models\VisitaMongo;
use App\Models\VisitaTeste;
use App\Solutions\Util\Util;
use Illuminate\Support\Facades\DB;

class TrackerService
{
    public function registraVisista($dados){

        //Verifica se visita existe
        $visita_exist = VisitaTeste::where('chave_sessao', $dados['k'])->get();

        //Cria visita
        if(empty(@$visita_exist[0]['chave_sessao'])){

            $ip = $dados['ip'];
            $dados['localizacao'] = (array) json_decode( file_get_contents("http://ip-api.com/json/$ip") );
            
            $origem = '';
        
            if (strpos($dados['referrer'], 'google') !== false) {
                
                //Anúncio google
                if (strpos($dados['url'], 'gclid') !== false) {
                    $origem = 'anuncio_google';
                //Busca google
                }else{
                    $origem = 'google';
                }
            
            //Direto
            }else{
                $origem = 'direto';
            }
        
            $visita = [
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
                'referrer' => $dados['referrer'],
                'resolucao' => $dados['resolucao'],
                'os' => $dados['os'],
                "paginas" => [
                    [
                    'hora' => date('Y-m-d H:i:s'), 
                    'url' => $dados['url']
                    ]
                ]
            ];

            VisitaTeste::create($visita);

        //Atualiza URLs
        }else{

            $paginas = $visita_exist[0]['paginas'];
            $atualizar = true;
            
            foreach($paginas as $v){
                if($v['url'] == $dados['url']){
                    $atualizar = false;
                    break;
                }
            }

            if($atualizar){
                $paginas_atualizar = $visita_exist[0]['paginas'];
                $paginas_atualizar[] = ['hora' => date('Y-m-d H:i:s'), 'url' => $dados['url'] ];

                VisitaTeste::where( array ('chave_sessao' => $dados['k']) )->update([ 'paginas' => $paginas_atualizar], ['upsert' => true] );
            }
        }


        return json_encode(["sucess" => true]);
    }
}
