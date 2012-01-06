<?php

defined('BW') or die("Acesso negado!");

class bwComponent extends bwComponentOld
{
    // variaveis ADM
    var $adm_nome = 'Componente sem nome';
    var $adm_titlo = 'Componente sem título';
    var $adm_pagina_padrao = 'adm.php?com=xxxx&view=yyyy';
    var $adm_menu_visivel = true;
    
    //
    public function __construct()
    {
        parent::__construct();
    }

    public function getItemid()
    {
        return bwRequest::getVar('itemid', null);
    }

    public function getConfig($prefix)
    {
        if(is_null($this->get('_config')))
        {
            $c = new bwConfigDB();
            $c->setPrefix('component.'.$prefix);

            $this->set('_config', $c);
        }

        return $this->get('_config');
    }



}
?>
