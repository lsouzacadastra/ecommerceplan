<?php

namespace App\Solutions\Util;

use App\Models\Visita;
use App\Models\VisitaMongo;

use DateTime;
use MongoDB\BSON\UTCDatetime;
use Carbon\Carbon;

class Util
{
    /*
    *  Formata datas para o padrão da consulta do MongoDb
    * @param: $data - data a ser convertinda, vinda no formato YYYY-mm-dd
    * @param: $demaisum - se true acrescenta mais um dia na data, util para consultas de de-ate, aplicado na data "até" QUANDO NÃO É ESTIPULADO HORÁRIO NAS DATA
    * @return: $data_saida - data convertida para utc date time aceita pelo mongo
    */
    public static function formatDataParaMongo($data, $demaisum = false)
    {

        if ($demaisum)
            $adicionar_um_dia = 86399;
        else
            $adicionar_um_dia = 0;

        $data_saida = Carbon::createFromDate($data)->timestamp;
        $data_saida = new UTCDateTime(($data_saida + $adicionar_um_dia) * 1000);

        return $data_saida;
    }

    /*
    *  Formata datas vinda de consulta ao banco MongoDB
    * @param: $data - data a ser convertinda
    * @return: $data_saida - data no formato YYYY-mm-dd
    */
    public static function formatDataVindaMongo($data)
    {
        $data = $data->toDateTime();
        $data = (array)$data;
        $data = substr($data['date'], 0, 10);
        return $data;
    }

    /*
    * @param $url - url a ser tratada
    * @return - retorna a origem/midia correspondente da url
    */
    public function trataOrigemMidia($url, $referrer)
    {
        $nome_site_referrer = $this->limpaUrl($referrer);
        $parametros_url = $this->getVariaveisUrl($url);

        //Lista por nome do domínio
        $this->lista_social = ['facebook', 'instagram'];
        $this->lista_seo = ['search.yahoo', 'google', 'bing', 'duckduckgo'];
        
        //Lista por parâmetro da url
        $this->lista_cpc = ['gclid', 'gclsrc'];
        $this->lista_campanha = ['campaignSource'];
        $this->lista_utm = ['utm_source'];

        //Particiona a URL
        $comparador_origem = preg_split('/[ !,.?.=]+/', $referrer);
        $comparador_url = preg_split('/[ !,.?.=]+/', $url);

        $source = '';
        $medium = '';

        if (count(array_intersect($this->lista_cpc, $comparador_url)) > 0) {
            $source = 'google';
            $medium = 'cpc';
        
        }else if (count(array_intersect($this->lista_campanha, $comparador_url)) > 0) {
            
            if(array_key_exists('campaignMedium', $parametros_url)){
                $medium = $parametros_url['campaignMedium'];
            }else{
                $medium = "direct";
            }

            $source = $parametros_url['campaignSource'];
        
        }else if (count(array_intersect($this->lista_utm, $comparador_url)) > 0) {
            
            if(array_key_exists('utm_medium', $parametros_url)){
                $medium = $parametros_url['utm_medium'];
            }else{
                $medium = "direct";
            }

            if(!empty($parametros_url['utm_source'])){
                $source = $parametros_url['utm_source'];
            }else{
                $source = $nome_site_referrer;
            }
            
        
        }else if (count(array_intersect($this->lista_social, $comparador_origem)) > 0) {

            $medium = 'Social';
            $source = current(array_intersect($this->lista_social, $comparador_origem));
        
        } else if (count(array_intersect($this->lista_seo, $comparador_origem)) > 0) {

            $medium = 'organic search';
            $source = current(array_intersect($this->lista_seo, $comparador_origem));
        
        } else {
            if (empty($referrer) || $referrer === 'N/A') {
                $medium = 'direct';
                $source = 'direct';
            } else {
                $medium = 'referral';
                $source = $nome_site_referrer;
            }
        }

        if(empty($medium)){
            $medium = 'direct';
        }

        if(empty($source)){
            $source = 'direct';
        }

        return ['midia' => $medium, 'origem' => $source];
    }

     /*
    * @param $path - ex: /contador/index.html
    * @param $search - ex: ?utm_source=google&utm_medium=cpc&utm_campaign=sle&utm_content=buy-now&utm_term=term
    * @param $referrer - site de origem da visita
    * @return - retorna a origem/midia correspondente da url
    */
    public function trataOrigemMidiaPath($path, $search, $referrer)
    {
        $nome_site_referrer = $this->limpaUrl($referrer);
        $parametros_url = $this->getVariaveisUrl($search);

        //Lista por nome do domínio
        $this->lista_social = ['facebook', 'instagram'];
        $this->lista_seo = ['search.yahoo', 'google', 'bing', 'duckduckgo'];
        
        //Lista por parâmetro da url
        $this->lista_cpc = ['gclid', 'gclsrc'];
        $this->lista_campanha = ['campaignSource'];
        $this->lista_utm = ['utm_source'];

        //Particiona a URL
        $comparador_origem = preg_split('/[ !,.?.=]+/', $referrer);
        $comparador_search = preg_split('/[ !,.?.=]+/', $search);

        $source = '';
        $medium = '';

        if (count(array_intersect($this->lista_cpc, $comparador_search)) > 0) {
            $source = 'google';
            $medium = 'cpc';
        
        }else if (count(array_intersect($this->lista_campanha, $comparador_search)) > 0) {
            
            if(array_key_exists('campaignMedium', $parametros_url)){
                $medium = $parametros_url['campaignMedium'];
            }else{
                $medium = "direct";
            }

            $source = $parametros_url['campaignSource'];
        
        }else if (count(array_intersect($this->lista_utm, $comparador_search)) > 0) {
            
            if(array_key_exists('utm_medium', $parametros_url)){
                $medium = $parametros_url['utm_medium'];
            }else{
                $medium = "direct";
            }

            if(!empty($parametros_url['utm_source'])){
                $source = $parametros_url['utm_source'];
            }else{
                $source = $nome_site_referrer;
            }
            
        
        }else if (count(array_intersect($this->lista_social, $comparador_origem)) > 0) {

            $medium = 'Social';
            $source = current(array_intersect($this->lista_social, $comparador_origem));
        
        } else if (count(array_intersect($this->lista_seo, $comparador_origem)) > 0) {

            $medium = 'organic search';
            $source = current(array_intersect($this->lista_seo, $comparador_origem));
        
        } else {
            if (empty($referrer) || $referrer === 'N/A') {
                $medium = 'direct';
                $source = 'direct';
            } else {
                $medium = 'referral';
                $source = $nome_site_referrer;
            }
        }

        if(empty($medium)){
            $medium = 'direct';
        }

        if(empty($source)){
            $source = 'direct';
        }

        return ['midia' => $medium, 'origem' => $source];
    }

    /*
    * @param $url - url a ser tratada
    * @return - retorna somente o domínio da url
    */
    public static function limpaUrl($url)
    {
        $url = str_replace('https://', '', $url);
        $url = str_replace('http://', '', $url);
        $url = str_replace('www.', '', $url);
        $url = explode("/", $url);

        return $url[0];
    }

    /*
    * @param $url - url a ser tratada
    * @return - retorna somente o nome do site, por exemplo http://google.com.br/?q=meu_site retorna google
    */
    public static function nomeSiteUrl($url)
    {
        $url = str_replace('https://', '', $url);
        $url = str_replace('http://', '', $url);
        $url = str_replace('www.', '', $url);
        $url = explode("/", $url);
        
        $url = parse_url($url[0]);
        $url = $url['path'];
    
        $url = explode('.', $url);  
        
        if(strlen($url[0]) < 4){
            $url = $url[0].'.'.$url[1];
        }else{
            $url = $url[0];
        }

        return $url;
    }

     /*
    * @param $url - url a ser tratada
    * @return - retorna somente o nome do site, por exemplo http://google.com.br/?q=meu_site retorna google
    */
    public static function getVariaveisUrl($url)
    {
        $url = parse_url($url);

        if(!empty($url['query'])){
            $par = $url['query'];
            parse_str($par, $parametros);
        }else{
            $parametros = [];
        }
        

        return $parametros;
    }
}
