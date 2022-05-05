<?php

namespace App\Solutions\Painel;

use App\Services\PainelService;
use App\Solutions\Util\Util;

class Painel
{
    public $visitas = 0;
    public $dispositivos = [];
    public $urls = [];
    public $ips = [];
    public $os = [];
    public $origem = [];
    public $referrer = [];
    public $midias = [];
    public $grafico_totalizador = [];
    public $midias_origem = [];
    public $trafego = [];


    public function graficoMultiplo(){

        $dados_preparado = [];
        $parametro = 0;
        $alinhador = 3000;
        $espacador = 350;

        $dados = [
            // 'Fundo' => [
            //     'sem1' => 1000, 
            //     'sem2' => 1000, 
            //     'sem3' => 1000, 
            //     'sem4' => 1000, 
            //     'sem5' => 1000, 
            //     'sem6' => 1000, 
            //     'sem7' => 1000, 
            //     'sem8' => 1000
            // ],

            'Visitas' => [
                'sem1' => 35959, 
                'sem2' => 41058, 
                'sem3' => 60040, 
                'sem4' => 35568, 
                'sem5' => 29317, 
                'sem6' => 23383, 
                'sem7' => 24140, 
                'sem8' => 19712
            ],

            'Conversão' => [
                'sem1' => 0.23, 
                'sem2' => 0.17, 
                'sem3' => 0.11, 
                'sem4' => 0.15, 
                'sem5' => 0.19, 
                'sem6' => 0.32, 
                'sem7' => 0.34, 
                'sem8' => 0.25
            ],

            'Pedidos' => [
                'sem1' => 83, 
                'sem2' => 70, 
                'sem3' => 69, 
                'sem4' => 52, 
                'sem5' => 56, 
                'sem6' => 75, 
                'sem7' => 83, 
                'sem8' => 49
            ],

            'Ticket Médio' => [
                'sem1' => 2616, 
                'sem2' => 2097, 
                'sem3' => 2880, 
                'sem4' => 2686, 
                'sem5' => 2698, 
                'sem6' => 2230, 
                'sem7' => 2170, 
                'sem8' => 2272
            ],

            'Captado' => [
                'sem1' => 217149, 
                'sem2' => 146820, 
                'sem3' => 198767, 
                'sem4' => 139396, 
                'sem5' => 121131, 
                'sem6' => 167274, 
                'sem7' => 180130, 
                'sem8' => 111353
            ],

            'Aprovação' => [
                'sem1' => 51.82, 
                'sem2' => 64.18, 
                'sem3' => 35.11, 
                'sem4' => 25.28, 
                'sem5' => 42.38, 
                'sem6' => 67.87, 
                'sem7' => 74.62, 
                'sem8' => 56.65
            ],

            'Faturado' => [
                'sem1' => 112526, 
                'sem2' => 94228, 
                'sem3' => 69788, 
                'sem4' => 35313, 
                'sem5' => 64050, 
                'sem6' => 113527, 
                'sem7' => 134516, 
                'sem8' => 63080
            ],

            'Análise' => [
                'sem1' => 112526, 
                'sem2' => 94228, 
                'sem3' => 69788, 
                'sem4' => 35313, 
                'sem5' => 64050, 
                'sem6' => 113527, 
                'sem7' => 134516, 
                'sem8' => 63080
            ]
        ];

        $lista_cor = ['Visitas' => '#7cb5ec',
                    'Conversão' => '#3d95e9',
                    'Pedidos' => '#ff9b00fa',
                    'Ticket Médio' => '#d46200fa',
                    'Captado' => '#0af411',
                    'Aprovação' => '#009104',
                    'Faturado' => '#BF0B23', 
                    'Análise' => '#000000'];

        //$dados = array_reverse($dados);

        $valor_anterior = 0;
        $valor_calculado_anterior = 0;
        $lista_extra_information = [];


        $lista_maior = [];
        $lista_menor = [];
        $zona = [];
        $contador_zona = 1;
        $lista_extra_information_analise = [];

        foreach($dados as $k => $v){

            if($k == 'Faturado'){
                 $alinhador = 600;
                 $espacador = 200;
            }
            
            $margem = 0;

            foreach($v as $key => $value){

                if($key == 'sem1'){
                    $valor_calculado = $alinhador;
                    $valor_calculado_anterior = $alinhador;
                    $alinhador -= $espacador;

                    $margem = 0;
                    $cor = '#000000';

                }else{
                    
                    $porcentagem = ($value * 100) / $valor_anterior;

                    //dd([$valor_anterior, $value, $porcentagem]);
                    $valor_calculado = ($valor_calculado_anterior * $porcentagem) / 100;
                    $valor_calculado_anterior = $valor_calculado;

                    //Margem - indica a variação em relação ao mês anterior
                    $margem = 100 - number_format($porcentagem, 2, '.', '');

                    if($margem < 0){
                        $margem = $margem * -1;

                        if($k != 'Faturado' && $k != 'Análise'){
                            if(@$lista_maior[$key]['valor'] < $margem || empty($lista_maior[$key])){
                                

                                // echo "maior ".@$lista_maior[$key]['op'],'--'.$k.'--'.$margem, $key;
                                // echo "<br>";
                                $lista_maior[$key]['op']= $k;
                                $lista_maior[$key]['valor']= $margem;
                            }
                        }
                        
                        $margem = " + ".$margem;
                        $cor = '#038d1a';

                    }else{

                        if($k != 'Faturado' && $k != 'Análise'){
                            if(@$lista_menor[$key]['valor'] < $margem || empty($lista_menor[$key])){

                                // echo "maior ".@$lista_maior[$key],'--'.$k.'--'.$margem,'---',$key;
                                // echo "<br>";

                                $lista_menor[$key]['op']= $k;
                                $lista_menor[$key]['valor']= $margem;
                            }
                        }
                        
                        $margem = " - ".$margem;
                        $cor = '#a70101';
                    }

                    if($k == 'Faturado'){
                        //Caiu
                        if($value < $valor_anterior){
                            $color = $lista_cor[$lista_menor[$key]['op']];
                            
                            $label_analise = "(".$lista_menor[$key]['op'].") ".$lista_extra_information[$lista_menor[$key]['op']][$key];
                            $lista_extra_information_analise[$key] = $label_analise;
                        }else{
                            $color = $lista_cor[$lista_maior[$key]['op']];
                            $label_analise = "(".$lista_maior[$key]['op'].") ".$lista_extra_information[$lista_maior[$key]['op']][$key];
                            $lista_extra_information_analise[$key] = $label_analise;
                        }

                        $zone[] = [ 'value' => $contador_zona,
                                    'color' => $color];

                        $contador_zona ++;
                    }

                    if($k == 'Análise'){
                        //Caiu
                        if($value < $valor_anterior){
                            $color = $lista_cor[$lista_menor[$key]['op']];
                            $label_analise = "(".$lista_menor[$key]['op'].") ".$lista_extra_information[$lista_menor[$key]['op']][$key];
                        }else{
                            $color = $lista_cor[$lista_maior[$key]['op']];
                            $label_analise = "(".$lista_menor[$key]['op'].") ".$lista_extra_information[$lista_maior[$key]['op']][$key];
                        }
                    }
                }

                $lab_margem = "<span style='color: $cor'>$margem%</span>";

                $valor_anterior = $value;

                if($k != 'Pedidos')
                    $extra_information = number_format($value, 2, ',', '.');
                else
                    $extra_information = $value;

                if($k == 'Faturado' || $k == 'Captado' || $k == 'Ticket Médio')
                    $extra_information = "R$ $extra_information";
                
                if($k == 'Conversão' || $k == 'Aprovação')
                    $extra_information = "$extra_information %";

                $extra_information = "<b>$extra_information</b>";
                $extra_information = "<br/> <span style='font-size: 14pt'> <b>$extra_information</b> </span>";

                $extra_information .= " ($lab_margem)";
                

                $lista_extra_information[$k][$key] = $extra_information;

                if($k == 'Análise'){
                    if(!empty(@$lista_extra_information_analise[$key])){
                        $extra_information = $lista_extra_information_analise[$key];
                    }
                }

                //Formato antigo, sem extra information
                //$dados_calculados[$k][$key] = $valor_calculado;

                //$dados_calculados[$k][$key] = $valor_calculado;

                //Formato novo com extra information
                $item = ['y' => number_format($valor_calculado, 2, '.', ''), 
                         'custom' => ['extraInformation' => $extra_information ]
                ];

                $dados_calculados[$k][$key] = $item;
                
            }
        }

        //die;

        ksort($lista_maior);
        ksort($lista_menor);
        
        //dd([$lista_maior, $lista_menor]);
        /*
        35 - 100
        41 - x
        35x = 41 * 100
        x = (41 * 100) / 35
        */

        $grafico_multiplo = [];

        foreach($dados_calculados as $k => $v){

            $valores = [];

            foreach($v as $key => $value){
                $valores[] = $value;
            }

            $item_grafico = ['name' => $k, 'data' => $valores, 'lineWidth' ];

            if($k == 'Faturado'){
                $item_grafico['lineWidth'] = 9;
            }

            if($k == 'Análise'){
                $item_grafico['lineWidth'] = 4;
            }

            
            if($k == 'Visitas' || $k == 'Pedidos' || $k == 'Aprovação'){
                $item_grafico['lineWidth'] = 4;
            }

            $item_grafico['color'] = $lista_cor[$k];

            
            if($k == 'Análise'){
                $item_grafico['zoneAxis'] = 'x';

                // $zone[] = [ 'value' => 2,
                //             'color' => '#f7a35c'];

                // $zone[] = [ 'value' => 4,
                //             'color' => '#000000'];

                // $zone[] = [ 'value' => 6,
                //             'color' => '#d46200fa'];

                $item_grafico['zones'] = $zone;

            }

            $grafico_multiplo[] = $item_grafico;
        }

        return $grafico_multiplo;
    }

    public function getDadosPainel($data_de, $data_ate)
    {

        $time_de = strtotime($data_de);
        $time_ate = strtotime($data_ate);

        $painel = new PainelService();
        $this->util = new Util();
        $dados = $painel->getDados($data_de, $data_ate);

        $dados->lazy()->each(function ($v) {

            //Exemplo utilizando Chunk
            //$resultado->each(function ($v) {

            //Contabilização das datas
            $data = Util::formatDataVindaMongo($v->data);
            @$this->grafico_totalizador[$data]++;
            
            //Urls
            $urle = explode("?", @$v->url);
            @$this->urls[$urle[0]]++;
            
            //Dispositivos
            @$this->dispositivos[$v->dispositivo]++;

            //Referrer
            @$v->referrer = Util::limpaUrl($v->referrer);

            $v->referrer = rtrim($v->referrer, '/');
            
            
            if(empty($v->referrer))
                @$this->referrer['direct']++;
            else    
            @$this->referrer[$v->referrer]++;

            //Trata a origem
            $origemMidia = $this->util->trataOrigemMidia($v->url, $v->referrer);
            
            $origem = $origemMidia['origem'];
            $midia = $origemMidia['midia'];

            @$this->midias_origem[$origem . '/' . $midia]++;
            
            @$this->trafego[$midia]++;

            //Sessões
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

        //Tratamento gráfico
        for ($i = $time_de; $i <= $time_ate; $i += 86400) {
            $data_legenda = date('d/m/Y', $i);
            $data_banco = date('Y-m-d', $i);

            $this->grafico_legenda[] = "'$data_legenda'";
            $this->grafico_dados[] = (int)@$this->grafico_totalizador[$data_banco];
        }

        $this->grafico_dados = implode(',', $this->grafico_dados);
        $this->grafico_legenda = implode(',', $this->grafico_legenda);
        //

        //Gráfico de Midia x Origem
        $this->grafico_midia_origem = [];

        foreach (@$this->midias_origem as $k => $v) {
            $this->grafico_midia_origem[] = ["name" => $k, 'data' => [$v]];
        }

        $this->grafico_midia_origem = json_encode($this->grafico_midia_origem, JSON_NUMERIC_CHECK);
        
        //Gráfico de Midia
        $this->grafico_trafego = [];

        foreach ($this->trafego as $k => $v) {
            $this->grafico_trafego[] = ["name" => $k, 'data' => [$v]];
        }
 
        $this->grafico_trafego = json_encode($this->grafico_trafego, JSON_NUMERIC_CHECK);

        
        //Gráfico de Referrer
        $this->grafico_referrer = [];

        foreach ($this->referrer as $k => $v) {
            $this->grafico_referrer[] = ["name" => $k, 'data' => [$v]];
        }

        $this->grafico_referrer = json_encode($this->grafico_referrer, JSON_NUMERIC_CHECK);

        //Últimos 10 acessos
        //$ultimos_acessos = array_reverse((array)$dados);
        $this->lista_ultimos_acessos = [];

        //pageViews
        $this->pageViews = 0;

        //URLs
        asort($this->urls);
        $this->urls = array_reverse($this->urls);

        $num_urls = 0;

        foreach ($this->urls as $k => $v) {
            $num_urls++;

            if ($num_urls > 10) {
                unset($this->urls[$k]);
            }
        }

        //Ordenamento e contabilização dos dados
        asort($this->origem);
        $this->origem = array_reverse($this->origem);

        asort($this->referrer);
        $this->referrer = array_reverse($this->referrer);

        asort($this->os);
        $this->os = array_reverse($this->os);

        asort($this->midias_origem);
        $this->midias_origem = array_reverse($this->midias_origem);

        asort($this->trafego);
        $this->trafego = array_reverse($this->trafego);

        $this->visitantes_unicos = count($this->ips);
    }
}
