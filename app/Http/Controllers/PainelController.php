<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use App\Models\VisitaMongo;
use Illuminate\Support\Facades\DB;
use App\Solutions\Tracker\Tracker;


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

    public function painel()
    {   
        $data_de = date('Y-m-d', strtotime('today -10 days'));
        $data_ate = date('Y-m-d', strtotime('today'));
        
        $data_de = '2022-04-12';
        $data_ate = '2022-04-12';

        $tracker = new Tracker();
        $tracker->getDadosPainel($data_de, $data_ate);

        view()->share('pageViews', $tracker->pageViews);
        view()->share('visitas', $tracker->visitas);
        view()->share('urls', $tracker->urls);
        view()->share('origem', $tracker->origem);
        view()->share('referrer', $tracker->referrer);
        view()->share('os', $tracker->os);
        view()->share('visitantes_unicos', $tracker->visitantes_unicos);
        view()->share('dispositivos', $tracker->dispositivos);
        view()->share('lista_ultimos_acessos', $tracker->lista_ultimos_acessos);
        view()->share('midias', $tracker->midias);
        view()->share('grafico_dados', $tracker->grafico_dados);
        view()->share('grafico_legenda', $tracker->grafico_legenda);
        view()->share('grafico_media', $tracker->grafico_media);
        view()->share('grafico_referrer', $tracker->grafico_referrer);
        return view('painel', []);
    }

    public function index()
    {
        return redirect('painel/painel');
    }
}
