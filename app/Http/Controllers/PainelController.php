<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use App\Models\VisitaMongo;
use Illuminate\Support\Facades\DB;
use App\Solutions\Painel\Painel;
use App\Solutions\Util\Util;

class PainelController extends Controller
{
    public function multiplicador()
    {
        die('Erro, ativar multiplicador');
        
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

    public function painel()
    {   
        $data_de = date('Y-m-d', strtotime('today -30 days'));
        $data_ate = date('Y-m-d', strtotime('today'));
        
        $painel = new Painel();
        $painel->getDadosPainel($data_de, $data_ate);

        view()->share('pageViews', $painel->pageViews);
        view()->share('visitas', $painel->visitas);
        view()->share('urls', $painel->urls);
        view()->share('origem', $painel->origem);
        view()->share('referrer', $painel->referrer);
        view()->share('os', $painel->os);
        view()->share('visitantes_unicos', $painel->visitantes_unicos);
        view()->share('dispositivos', $painel->dispositivos);
        view()->share('lista_ultimos_acessos', $painel->lista_ultimos_acessos);
        view()->share('grafico_dados', $painel->grafico_dados);
        view()->share('grafico_legenda', $painel->grafico_legenda);
        view()->share('grafico_referrer', $painel->grafico_referrer);

        view()->share('grafico_midia_origem', $painel->grafico_midia_origem);
        view()->share('midias_origem', $painel->midias_origem);

        view()->share('grafico_trafego', $painel->grafico_trafego);
        view()->share('trafego', $painel->trafego);

        return view('painel', []);
    }

    public function graficoMultiplo(){
        $painel = new Painel();
        $grafico = $painel->graficoMultiplo();
        $grafico = json_encode($grafico, JSON_NUMERIC_CHECK);

        $grafico = str_replace("'", '@@', $grafico);
        $grafico = str_replace('"', "'", $grafico);
        $grafico = str_replace("@@", '"', $grafico);
        
        view()->share('grafico', $grafico);

        return view('multiplo', []);
    }   

    public function index()
    {   
        $util = new Util();
        $foo = $util->trataOrigemMidiaPath('/contador/index.html', '?utm_source=google&utm_medium=cpc&utm_campaign=sle&utm_content=buy-now&utm_term=term', 'http://localhost/contador/link.html');
        
        dd($foo);
        die('foo');
        return redirect('painel/painel');
    }
}
