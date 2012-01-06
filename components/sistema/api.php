<?

defined('BW') or die("Acesso negado!");

class bwSistema extends bwComponent
{
    // variaveis ADM
    var $adm_nome = 'Sistema';
    var $adm_pagina_padrao = '';
    var $adm_menu_visivel = false;


    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
}
?>
