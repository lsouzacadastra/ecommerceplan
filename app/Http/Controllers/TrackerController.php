<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TrackerService;
use App\Solutions\Tracker\Tracker;

class TrackerController extends Controller
{

    private $ARQUIVO = 'visitas.json';

    public function collect(){
        
        //Libera o CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Max-Age: 3600');
        
        //Default time
        date_default_timezone_set('America/Sao_Paulo');
       
        $dados = json_decode(file_get_contents('php://input'), true);
        
        if(empty($dados['k'])){
            $retorno =  json_encode(["sucess" => false]);
        }else{
            $tracker = new TrackerService();
            $retorno = $tracker->registraVisista($dados);
        }
        
        return $retorno;
    }

    public function index(){
    
        //Libera o CORS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Max-Age: 3600');

        //Default time
        date_default_timezone_set('America/Sao_Paulo');
       
        $dados = json_decode(file_get_contents('php://input'), true);
    
        if(empty($dados['k'])){
            echo json_encode(["sucess" => false]);
            die;
        }
        
        $ip                   = $dados['ip'];
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
            'data' => date('Y-m-d'), 
            'hora' => date('H:i:s'), 
            'url' => $dados['url'],
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
            'os' => $dados['os']
        ];
    
        //Se arquivo não existir ele vai ser criado
        if(!file_exists($this->ARQUIVO)){
            $handle = fopen( $this->ARQUIVO, 'w' );
            fwrite( $handle, '[]' );
            fclose($handle);
        }
    
        //Leitura do arquivo
        $visitas_contabilizadas = file_get_contents( $this->ARQUIVO );
        $visitas_contabilizadas = json_decode($visitas_contabilizadas);
        
        $visitas_contabilizadas[] = $visita;
        $visitas_contabilizadas = json_encode($visitas_contabilizadas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
        $handle = fopen( $this->ARQUIVO, 'w' );
        fwrite( $handle, $visitas_contabilizadas );
        fclose($handle);
    
        return json_encode(["sucess" => true]);
    }

    public function geraTag(){
        $tag = Tracker::geraTag();
        return $tag;
    }
}