<?
defined('BW') or die("Acesso negado!");

$tituloPage = "Configurações do sistema";

$menu = array(
        'core.site' => array(
            'url' => 'adm.php?com=sistema&view=configuracoes&tab=core.site',
            'tit' => 'Site (básico)'
        ),
        'core.adm' => array(
            'url' => 'adm.php?com=sistema&view=configuracoes&tab=core.adm',
            'tit' => 'Administração'
        ),
        'plugins' => array(
            'url' => 'adm.php?com=sistema&view=configuracoes&tab=plugins',
            'tit' => 'Plugins'
        ),
        'core.cache' => array(
            'url' => 'adm.php?com=sistema&view=configuracoes&tab=core.cache',
            'tit' => 'Cache'
        ),
        'core.debug' => array(
            'url' => 'adm.php?com=sistema&view=configuracoes&tab=core.debug',
            'tit' => 'Debug'
        ),
);



?>