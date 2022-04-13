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
    * @param: $demaisum - se true acrescenta mais um dia na data, util para consultas de de-ate
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
    public function trataOrigemMidia($url)
    {

        $this->lista_social = ['facebook', 'instagram'];
        $this->lista_seo = ['search.yahoo', 'google', 'bing'];

        $comparador_origem = preg_split('/[ !,.?]+/', $url);

        if (count(array_intersect($this->lista_social, $comparador_origem)) > 0) {
            return 'social';
        } else if (count(array_intersect($this->lista_seo, $comparador_origem)) > 0) {
            return 'seo';
        } else {
            if (empty($url) || $url === 'N/A') {
                return 'direto';
            } else {
                return 'referrer';
            }
        }
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
}
