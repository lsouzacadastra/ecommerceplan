<?php

namespace App\Services;

use App\Models\VisitaTeste;
use App\Solutions\Util\Util;
use App\Repositories\PainelRepository;

class PainelService
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
    public $util;


    public function getDadosPainel($data_de = false, $data_ate = false)
    {

        $time_de = strtotime($data_de);
        $time_ate = strtotime($data_ate);

        $painel = new PainelRepository();
        $this->util = new Util();
        $dados = $painel->getDadosPainel($data_de, $data_ate);

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
