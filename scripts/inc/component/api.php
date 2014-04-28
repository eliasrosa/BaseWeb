<?php

defined('BW') or die("Acesso negado!");

class bw%class% extends bwComponent
{

    // variaveis obrigatórias
    var $id = '%folder%';
    var $nome = '%titulo%';
    var $adm_visivel = true;

    // getInstance
    public function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
    
}
