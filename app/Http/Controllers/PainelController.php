<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use App\Models\VisitaMongo;
use App\Services\PainelService;
//use Jenssegers\Support\Facades\DB;
use PhpParser\Node\Stmt\Foreach_;
use Illuminate\Support\Facades\DB;

//Tratamento de datas
use DateTime;
use MongoDB\BSON\UTCDatetime;
use Carbon\Carbon;

class PainelController extends Controller
{

    public function multiplicador()
    {

        $dados = DB::table('visitas')->select('*')
            ->distinct('chave_sessao')
            ->get()
            ->toArray();

        foreach ($dados as $k => $v) {

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
                'chave_sessao' => rand(0, 50000),
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


    public function migracao()
    {

        $dados = (array) json_decode(file_get_contents("visitas.json"));

        // switch (json_last_error()) {
        //     case JSON_ERROR_NONE:
        //         echo ' - No errors';
        //     break;
        //     case JSON_ERROR_DEPTH:
        //         echo ' - Maximum stack depth exceeded';
        //     break;
        //     case JSON_ERROR_STATE_MISMATCH:
        //         echo ' - Underflow or the modes mismatch';
        //     break;
        //     case JSON_ERROR_CTRL_CHAR:
        //         echo ' - Unexpected control character found';
        //     break;
        //     case JSON_ERROR_SYNTAX:
        //         echo ' - Syntax error, malformed JSON';
        //     break;
        //     case JSON_ERROR_UTF8:
        //         echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        //     break;
        //     default:
        //         echo ' - Unknown error';
        //     break;
        // }

        foreach ($dados as $k => $v) {

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

    /*
    *  Formata datas para o padrão da consulta do MongoDb
    */
    public function formatData($data, $demaisum = false)
    {

        if ($demaisum)
            $adicionar_um_dia = 86399;
        else
            $adicionar_um_dia = 0;

        $data_saida = Carbon::createFromDate($data)->timestamp;
        $data_saida = new UTCDateTime(($data_saida + $adicionar_um_dia) * 1000);

        return $data_saida;
    }

    public function mongo()
    {

        $hoje = date('Y-m-d', strtotime('today'));
        $antes = date('Y-m-d', strtotime('today -14 days'));

        $hoje_time = strtotime($hoje);
        $antes_time = strtotime($antes);


        $grafico_legenda = [];
        $grafico_dados = [];
        $this->grafico_totalizador = [];


        $this->visitas = 0;
        $this->dispositivos = [];
        $this->urls = [];
        $this->ips = [];
        $this->os = [];
        $this->origem = [];
        $this->referrer = [];
        $this->midias = [];
        $this->grafico_totalizador = [];

        $this->lista_social = ['facebook', 'instagram'];
        $this->lista_seo = ['search.yahoo', 'google', 'bing'];


        $dados = VisitaMongo::select('chave_sessao', 'ip', 'data', 'url', 'dispositivo', 'referrer')
            ->whereBetween('data', array($this->formatData($antes), $this->formatData($hoje, true)))
            ->groupBy('chave_sessao');

      

        //$dados->chunk(100000, function ($resultado) {

            $dados->lazy()->each(function ($v) {
            //$resultado->each(function ($v) {

                $data = $v->data->toDateTime();
                $data = (array)$data;
                $data = substr($data['date'], 0, 10);

                @$this->grafico_totalizador[$data]++;

                $urle = explode("?", @$v->url);
                @$this->urls[$urle[0]]++;

                @$this->dispositivos[$v->dispositivo]++;

                @$v->referrer = str_replace('https://', '', @$v->referrer);
                @$v->referrer = str_replace('http://', '', @$v->referrer);
                @$v->referrer = str_replace('www.', '', @$v->referrer);
                @$v->referrer = explode("/", @$v->referrer);
                @$v->referrer = @$v->referrer[0];

                //Referrer
                if (empty($v->referrer)) {
                    $v->referrer = 'N/A';
                }

                $v->referrer = rtrim($v->referrer, '/');
                @$this->referrer[$v->referrer]++;

                $categorizado = false;

                foreach ($this->lista_social as $op) {

                    if (strpos($v->referrer, $op) !== false) {
                        @$this->midias['social']++;

                        $categorizado = true;
                        break;
                    }
                }

                if (!$categorizado) {

                    foreach ($this->lista_seo as $op) {

                        if (strpos($v->referrer, $op) !== false) {
                            @$this->midias['seo']++;

                            $categorizado = true;
                            break;
                        }
                    }
                }

                if (!$categorizado) {

                    if (empty($v->referrer) || $v->referrer === 'N/A') {
                        @$this->midias['direto']++;
                    } else {
                        @$this->midias['referrer']++;
                    }
                }
                //

                //Sessões
                //@$this->visitas[$v->chave_sessao] = @$v->chave_sessao;
                @$this->visitas++;

                //IPs
                @$this->ips[$v->ip]++;

                //OS
                if (empty($v->os)) {
                    $v->os = 'N/A';
                }
                @$this->os[$v->os]++;

                if (empty($v->origem)) {
                    $v->origem = 'direto';
                }

                //Referrer
                @$this->origem[$v->origem]++;
            });
       // });


        //Tratamento gráfico
        for ($i = $antes_time; $i <= $hoje_time; $i += 86400) {
            $data_legenda = date('d/m/Y', $i);
            $data_banco = date('Y-m-d', $i);

            $this->grafico_legenda[] = "'$data_legenda'";
            $this->grafico_dados[] = (int)@$this->grafico_totalizador[$data_banco];
        }


        $this->grafico_dados = implode(',', $this->grafico_dados);
        $this->grafico_legenda = implode(',', $this->grafico_legenda);
        //

        //Gráfico de Midia
        $this->grafico_media = [];

        foreach ($this->midias as $k => $v) {
            $this->grafico_media[] = ["name" => $k, 'data' => [$v]];
        }

        $this->grafico_media = json_encode($this->grafico_media, JSON_NUMERIC_CHECK);
        //


        //Gráfico de Referrer
        $this->grafico_referrer = [];

        foreach ($this->referrer as $k => $v) {
            $this->grafico_referrer[] = ["name" => $k, 'data' => [$v]];
        }

        $this->grafico_referrer = json_encode($this->grafico_referrer, JSON_NUMERIC_CHECK);


        //Últimos 10 acessos
        //$ultimos_acessos = array_reverse((array)$dados);

        $lista_ultimos_acessos = [];




        //pageViews
        $pageViews = 0;

        //Visitas
        //$visitas = count($this->visitas);

        asort($this->urls);
        $this->urls = array_reverse($this->urls);

        $num_urls = 0;

        foreach ($this->urls as $k => $v) {
            $num_urls++;

            if ($num_urls > 10) {
                unset($this->urls[$k]);
            }
        }

        asort($this->origem);
        $this->origem = array_reverse($this->origem);

        asort($this->referrer);
        $this->referrer = array_reverse($this->referrer);

        asort($this->os);
        $this->os = array_reverse($this->os);

        $visitantes_unicos = count($this->ips);

        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        ini_set('display_errors', 1);

        view()->share('pageViews', $pageViews);
        view()->share('visitas', $this->visitas);
        view()->share('urls', $this->urls);
        view()->share('origem', $this->origem);
        view()->share('referrer', $this->referrer);
        view()->share('os', $this->os);
        view()->share('visitantes_unicos', $visitantes_unicos);
        view()->share('dispositivos', $this->dispositivos);
        view()->share('lista_ultimos_acessos', $lista_ultimos_acessos);
        view()->share('midias', $this->midias);
        view()->share('grafico_dados', $this->grafico_dados);
        view()->share('grafico_legenda', $this->grafico_legenda);
        view()->share('grafico_media', $this->grafico_media);
        view()->share('grafico_referrer', $this->grafico_referrer);
        return view('painel', []);
    }

    public function index()
    {

        $hoje = strtotime('today');
        $antes = strtotime('today - 14 days');

        // $hoje  = strtotime('2022-04-04');
        // $antes = strtotime('2022-03-31');

        $grafico_legenda = [];
        $grafico_dados = [];
        $grafico_totalizador = [];

        $where = "data >= '" . date('Y-m-d', $antes) . "' AND data <= '" . date('Y-m-d', $hoje) . "'";

        $dados = DB::table('visitas')->select('chave_sessao', 'ip', 'data', 'url', 'dispositivo', 'referrer')
            //->distinct('chave_sessao', 'url')
            ->whereRaw("$where")
            ->groupBy('chave_sessao')
            ->get()
            ->toArray();



        // var_dump($grafico_dados);
        // die;

        //$where = "1";

        // $dados = DB::table('visitas')->select('*')
        //                             ->distinct('chave_sessao')
        //                              ->whereRaw("$where")
        //                              ->get()
        //                              ->toArray();

        // $dados_urls = DB::table('visitas')->select('chave_sessao', 'chave_sessao', 'url')
        //                              ->distinct('chave_sessao')
        //                              ->whereRaw("$where")
        //                              ->get()
        //                              ->toArray();

        // $dados_disp = DB::table('visitas')->select('chave_sessao', 'dispositivo', 'url')
        //                              ->distinct('chave_sessao')
        //                              ->whereRaw("$where")
        //                              ->get()
        //                              ->toArray();

        // $dados_referrer = DB::table('visitas')->select('chave_sessao', 'referrer', 'url')
        //                              ->distinct('chave_sessao')
        //                              ->whereRaw("$where")
        //                              ->get()
        //                              ->toArray();


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

        $lista_social = ['facebook', 'instagram'];
        $lista_seo = ['search.yahoo', 'google', 'bing'];

        foreach ($dados as $k => $v) {
            $v = (array)$v;

            @$grafico_totalizador[$v['data']]++;

            $urle = explode("?", @$v['url']);
            @$urls[$urle[0]]++;

            @$dispositivos[$v['dispositivo']]++;

            $v['referrer'] = str_replace('https://', '', $v['referrer']);
            $v['referrer'] = str_replace('http://', '', $v['referrer']);
            $v['referrer'] = str_replace('www.', '', $v['referrer']);
            $v['referrer'] = explode("/", $v['referrer']);
            $v['referrer'] = $v['referrer'][0];

            //Referrer
            if (empty($v['referrer'])) {
                $v['referrer'] = 'N/A';
            }

            $v['referrer'] = rtrim($v['referrer'], '/');
            @$referrer[$v['referrer']]++;

            $categorizado = false;

            foreach ($lista_social as $op) {

                if (strpos($v['referrer'], $op) !== false) {
                    @$midias['social']++;

                    $categorizado = true;
                    break;
                }
            }

            if (!$categorizado) {

                foreach ($lista_seo as $op) {

                    if (strpos($v['referrer'], $op) !== false) {
                        @$midias['seo']++;

                        $categorizado = true;
                        break;
                    }
                }
            }

            if (!$categorizado) {

                if (empty($v['referrer']) || $v['referrer'] === 'N/A') {
                    @$midias['direto']++;
                } else {
                    @$midias['referrer']++;
                }
            }
            //


            //Sessões
            @$visitas[$v['chave_sessao']] = @$v['chave_sessao'];

            //IPs
            @$ips[$v['ip']]++;

            //OS
            if (empty($v['os'])) {
                $v['os'] = 'N/A';
            }
            @$os[$v['os']]++;

            if (empty($v['origem'])) {
                $v['origem'] = 'direto';
            }

            //Referrer
            @$origem[$v['origem']]++;
        }

        //Tratamento gráfico
        for ($i = $antes; $i <= $hoje; $i += 86400) {
            $data_legenda = date('d/m/Y', $i);
            $data_banco = date('Y-m-d', $i);

            $grafico_legenda[] = "'$data_legenda'";
            $grafico_dados[] = (int)@$grafico_totalizador[$data_banco];
        }

        $grafico_dados = implode(',', $grafico_dados);
        $grafico_legenda = implode(',', $grafico_legenda);
        //

        //Gráfico de Midia
        $grafico_media = [];

        foreach ($midias as $k => $v) {
            $grafico_media[] = ["name" => $k, 'data' => [$v]];
        }

        $grafico_media = json_encode($grafico_media, JSON_NUMERIC_CHECK);
        //


        //Gráfico de Referrer
        $grafico_referrer = [];

        foreach ($referrer as $k => $v) {
            $grafico_referrer[] = ["name" => $k, 'data' => [$v]];
        }

        $grafico_referrer = json_encode($grafico_referrer, JSON_NUMERIC_CHECK);


        //Últimos 10 acessos
        //$ultimos_acessos = array_reverse((array)$dados);

        $lista_ultimos_acessos = [];

        // $registros = 0;

        // foreach($ultimos_acessos as $k => $v){
        //     $registros ++;

        //     if($registros > 10){
        //         break;
        //     }

        //     $lista_ultimos_acessos[] = (array)$v;
        // }


        //pageViews
        $pageViews = count($dados);

        //Visitas
        $visitas = count($visitas);

        asort($urls);
        $urls = array_reverse($urls);

        $num_urls = 0;

        foreach ($urls as $k => $v) {
            $num_urls++;

            if ($num_urls > 10) {
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
