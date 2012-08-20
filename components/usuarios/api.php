<?php

defined('BW') or die("Acesso negado!");

class bwUsuarios extends bwComponent
{

    // variaveis obrigatórias
    var $id = 'usuarios';
    var $nome = 'Usuários';
    var $adm_visivel = true;

    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
    
}

?>
