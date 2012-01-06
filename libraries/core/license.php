<?php

defined('BW') or die("Acesso negado!");

class bwLicense extends bwObject
{
    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    function create($tipo, $key1, $versao, $host, $ip, $data)
    {
        $tipo = strtoupper($tipo);

        if (!preg_match("#BW|BWDEV|BWLOC|BWTEST#", $tipo))
            return false;

        $data = str_replace('-', '', $data);
        $key1 = substr($key1, 0, 6);
        $key2 = substr(sha1("{$tipo}::{$versao}::{$key1}::{$data}"), 0, 7);
        $host = substr(sha1("{$key2}-{$host}-{$ip}"), 0, 12);
        $serial = strtoupper("{$tipo}-{$key1}-{$key2}-{$host}-{$data}");

        return $serial;
    }

    function verificarTodas()
    {
        $serial = bwConfig::$serial;
        $isOk = false;

        foreach(explode(';', $serial) as $s)
        {
            if($this->verificar(trim($s)))
                $isOk = true;
        }

        // verifica a licença
        if(!$isOk)
            bwError::show('Por favor entre em contato com o administrador do site e solicite sua licença.<br /><br />Obrigado!', 'Licença inválida!');

    }

    function verificar($serial)
    {
        $versao = bwCore::getVesion();

        if (strlen($serial) != 42)
            if (strlen($serial) != 39)
                return false;

        $host = $_SERVER['SERVER_NAME'];
        $ip = $_SERVER['SERVER_ADDR'];

        $s = array();
        list($s['tipo'], $s['key1'], $s['key2'], $s['host/ip'], $s['data']) = explode('-', $serial);

        $d = $s['data'];
        $s['data'] = "{$d{0}}{$d{1}}{$d{2}}{$d{3}}-{$d{4}}{$d{5}}-{$d{6}}{$d{7}}";

        if ($serial == $this->create($s['tipo'], $s['key1'], $versao, $host, $ip, $s['data']))
        {
            $dataAtual = strtotime(date("Y-m-d"));
            $serialData = strtotime($s['data']);

            if ($dataAtual <= $serialData)
                return true;
            else
                return false;
        }

        return false;
    }

}
?>
