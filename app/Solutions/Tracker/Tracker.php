<?php

namespace App\Solutions\Tracker;

use App\Services\TrackerService;
use App\Solutions\Util\Util;

class Tracker
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

    public function getDadosPainel($data_de, $data_ate)
    {

        $time_de = strtotime($data_de);
        $time_ate = strtotime($data_ate);

        $tracker = new TrackerService();
        $this->util = new Util();
        $dados = $tracker->getDados($data_de, $data_ate);

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

            if (empty($v->referrer)) {
                $v->referrer = 'N/A';
            }

            $v->referrer = rtrim($v->referrer, '/');
            @$this->referrer[$v->referrer]++;

            //Trata a origem
            $origem = $this->util->trataOrigemMidia($v->referrer);
            @$this->midias[$origem]++;


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

        //Gráfico de Midia
        $this->grafico_media = [];

        foreach ($this->midias as $k => $v) {
            $this->grafico_media[] = ["name" => $k, 'data' => [$v]];
        }

        $this->grafico_media = json_encode($this->grafico_media, JSON_NUMERIC_CHECK);
        

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

        $this->visitantes_unicos = count($this->ips);
    }
}
