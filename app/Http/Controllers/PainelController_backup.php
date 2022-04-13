<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;

class PainelController extends Controller
{

    public function multiplicador(){

        $dados = DB::table('visitas')->select('*')
        ->distinct('chave_sessao')
         ->get()
         ->toArray();

        foreach($dados as $k => $v){

           $hoje  = strtotime('today');
           $antes = strtotime('today - 30 days');
   
           $data = rand($antes, $hoje);
           $data = date('Y-m-d', $data);
            
            $v = (array) $v;

            $visita = Visita::create([
                
                'ip' => $v['ip'],
                'data' => $data,
                'hora' => $v['hora'],
                'url' => $v['url'],
                'chave_sessao' => rand(0,50000),
                'dispositivo' => $v['dispositivo'],
                'pais' => $v['pais'],
                'paisc' => $v['paisc'],
                'estado' => $v['estado'],
                'estadoc' => $v['estadoc'],
                'cidade' => $v['cidade'],
                'origem' => @$v['origem'],
                'referrer' => @$v['referrer'],
                'resolucao' => @$v['resolucao'],
                'os' => @$v['os']
            ]);
        }
    }


    public function migracao(){
        
        $dados = (array) json_decode( file_get_contents("visitas.json") );

        foreach($dados as $k => $v){
            
            $v = (array) $v;

            $visita = Visita::create([
                
                'ip' => $v['ip'],
                'data' => $v['data'],
                'hora' => $v['hora'],
                'url' => $v['url'],
                'chave_sessao' => $v['chave_sessao'],
                'dispositivo' => $v['dispositivo'],
                'pais' => $v['pais'],
                'paisc' => $v['paisc'],
                'estado' => $v['estado'],
                'estadoc' => $v['estadoc'],
                'cidade' => $v['cidade'],
                'origem' => @$v['origem'],
                'referrer' => @$v['referrer'],
                'resolucao' => @$v['resolucao'],
                'os' => @$v['os']
            ]);
        }

        return "migracao OK";
    }

    public function index(){
        
        $hoje = strtotime('today');
        $antes = strtotime('today - 30 days');
        $grafico_legenda = [];
        $grafico_dados = [];
        $grafico_totalizador = [];

        $where = "data >= '" . date('Y-m-d', $antes) . "' AND data <= '" . date('Y-m-d', $hoje)."'";

        $dados_grafico = DB::table('visitas')->select('chave_sessao', 'url', 'data')
                                            ->distinct('chave_sessao')
                                            ->whereRaw("$where")
                                            ->get()
                                            ->toArray();

        foreach($dados_grafico as $k => $v){
            $v = (array)$v;
            @$grafico_totalizador[$v['data']] ++;
        }
        
        for( $i = $antes; $i <= $hoje; $i += 86400 ){
            $data_legenda = date('d/m/Y', $i);
            $data_banco = date('Y-m-d', $i);

            $grafico_legenda[] = "'$data_legenda'";
            $grafico_dados[] = (int)@$grafico_totalizador[$data_banco];
        }

 
        $grafico_dados = implode(',', $grafico_dados);
        $grafico_legenda = implode(',', $grafico_legenda);

        // var_dump($grafico_dados);
        // die;

        //$where = "1";

        $dados = DB::table('visitas')->select('*')
                                    ->distinct('chave_sessao')
                                     ->whereRaw("$where")
                                     ->get()
                                     ->toArray();

        $dados_urls = DB::table('visitas')->select('chave_sessao', 'chave_sessao', 'url')
                                     ->distinct('chave_sessao')
                                     ->whereRaw("$where")
                                     ->get()
                                     ->toArray();

        $dados_disp = DB::table('visitas')->select('chave_sessao', 'dispositivo', 'url')
                                     ->distinct('chave_sessao')
                                     ->whereRaw("$where")
                                     ->get()
                                     ->toArray();
        
        $dados_referrer = DB::table('visitas')->select('chave_sessao', 'referrer', 'url')
                                     ->distinct('chave_sessao')
                                     ->whereRaw("$where")
                                     ->get()
                                     ->toArray();


        // die;
        // $dados = Visita::where("id", "IS NOT NULL")->get();
        //$dados = DB::table('visitas')->get();
        // //$dados = Visita::select("SELECT * FROM visistas LIMIT 1")->toArray();
        // $dados = Visita::all()->toArray();
        
        // Visita::chunk(2000000, function ($dados) {
        //     foreach($dados as $v){
        //         var_dump($v);
        //         die;
        //     }
        // });

        $visitas = [];
        $dispositivos = [];
        $urls = [];
        $ips = [];
        $os = [];
        $origem = [];
        $referrer = [];
        $midias = [];

        foreach($dados_urls as $k => $v){
            //Urls
            $v = (array)$v;
            $urle = explode("?", @$v['url']);
            @$urls[$urle[0]] ++;
        }

        foreach($dados_disp as $k => $v){
            $v = (array)$v;
            
            //Dispositivos
            @$dispositivos[$v['dispositivo']] ++;
        }

        $lista_social = ['facebook', 'instagram'];
        $lista_seo = ['search.yahoo', 'google', 'bing'];

        foreach($dados_referrer as $k => $v){
            $v = (array)$v;

            $v['referrer'] = str_replace('https://', '', $v['referrer']);
            $v['referrer'] = str_replace('http://', '', $v['referrer']);
            $v['referrer'] = str_replace('www.', '', $v['referrer']);
            $v['referrer'] = explode("/", $v['referrer']);
            $v['referrer'] = $v['referrer'][0];

            //Referrer
            if(empty($v['referrer'])){
                $v['referrer'] = 'N/A';
            }
            $v['referrer'] = rtrim($v['referrer'], '/');
            @$referrer[$v['referrer']] ++;

            $categorizado = false;

            foreach($lista_social as $op){
                
                if(strpos($v['referrer'], $op) !== false){
                    @$midias['social'] ++;

                    $categorizado = true;
                    break;
                }
            }

            if(!$categorizado){

                foreach($lista_seo as $op){

                    if(strpos($v['referrer'], $op) !== false){
                        @$midias['seo'] ++;

                        $categorizado = true;
                        break;
                    }
                }
            }

            if(!$categorizado){

                if(empty($v['referrer']) || $v['referrer'] === 'N/A'){
                    @$midias['direto'] ++;
                }else{
                    @$midias['referrer'] ++;
                }
            }
        }

        //Gráfico de Midia
        $grafico_media = [];

        foreach ( $midias as $k => $v){
            $grafico_media[] = ["name" => $k, 'data' => [$v]];
        }

        $grafico_media = json_encode($grafico_media, JSON_NUMERIC_CHECK);

        //Gráfico de dados Gerais
        foreach($dados as $k => $v){
            
            $v = (array)$v;
    
            //Sessões
            @$visitas[$v['chave_sessao']] = @$v['chave_sessao'];
            
            //IPs
            @$ips[$v['ip']] ++;
    
            //OS
            if(empty($v['os'])){
                $v['os'] = 'N/A';
            }
            @$os[$v['os']] ++;
    
            if(empty($v['origem'])){
                $v['origem'] = 'direto';
            }
    
            //Referrer
            @$origem[$v['origem']] ++;
        }
        

        //Gráfico de Referrer
        $grafico_referrer = [];

        foreach ( $referrer as $k => $v){
            $grafico_referrer[] = ["name" => $k, 'data' => [$v]];
        }

        $grafico_referrer = json_encode($grafico_referrer, JSON_NUMERIC_CHECK);

        $lista = [];
    
        foreach($dados as $k => $v){
    
            //Urls
            $v = (array)$v;
    
            @$lista[$v['chave_sessao']][] = $v;
        }
        
        //Últimos 10 acessos
        $ultimos_acessos = array_reverse((array)$dados);
        $lista_ultimos_acessos = [];
        $registros = 0;
        foreach($ultimos_acessos as $k => $v){
            $registros ++;
    
            if($registros > 10){
                break;
            }
    
            $lista_ultimos_acessos[] = (array)$v;
        }
       
    
        //pageViews
        $pageViews = count($dados);
        
        //Visitas
        $visitas = count($visitas);
        
        asort($urls);
        $urls = array_reverse($urls);
    
        $num_urls = 0;
    
        foreach($urls as $k => $v){
            $num_urls ++;
    
            if($num_urls > 10){
                unset($urls[$k]);
            }
        }
    
        asort($origem);
        $origem = array_reverse($origem);
    
        asort($referrer);
        $referrer = array_reverse($referrer);
    
        asort($os);
        $os = array_reverse($os);
    
        $visitantes_unicos = count($ips);
        
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        ini_set('display_errors', 1);

        view()->share('pageViews', $pageViews);
        view()->share('visitas', $visitas);
        view()->share('urls', $urls);
        view()->share('origem', $origem);
        view()->share('referrer', $referrer);
        view()->share('os', $os);
        view()->share('visitantes_unicos', $visitantes_unicos);
        view()->share('dispositivos', $dispositivos);
        view()->share('lista_ultimos_acessos', $lista_ultimos_acessos);
        view()->share('midias', $midias);
        view()->share('grafico_dados', $grafico_dados);
        view()->share('grafico_legenda', $grafico_legenda);
        view()->share('grafico_media', $grafico_media);
        view()->share('grafico_referrer', $grafico_referrer);
        return view('painel', []);
    }
}
