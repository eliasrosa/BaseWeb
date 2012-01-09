<?php

defined('BW') or die("Acesso negado!");

class bwCore
{
    function getConexao()
    {
        return Doctrine_Manager::getInstance()->getConnection('default');    
    }
    
    function __construct()
    {
        header('content-type: text/html; charset: utf-8');
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');
        mb_language('uni');
        mb_regex_encoding('UTF-8');
    }

    function init()
    {   
        // verifica todas as licenças
        //bwLicense::getInstance()->verificarTodas();

        // modo offline
        if (bwCore::getConfig()->getValue('site.offline') && !bwLogin::getInstance()->isLogin())
            bwUtil::redirect(BW_URL_ADM_LOGIN);

        // pega todos os menus
        bwMenu::getInstance()->getAll();

        // quebra url, encontra itemid pelo alias
        bwRouter::parseRoute();

        // verifica se o componente existe
        bwComponent::check();

        // verifica se o view do componente existe
        bwComponent::checkView();

        // executa as açoes do adm
        bwAdm::execTask();

        // Carrega o template
        bwTemplate::getInstance()->carregar();

        // carega titulo do menu
        bwHtml::getTituloMenu();

        // carrega o componente para o buffer
        bwBuffer::getInstance()->carregarHtmlComponente();

        // carrega os todos os modulos para buffer
        bwBuffer::getInstance()->carregarHtmlModulos();

        // carrega os js e css do adm
        bwAdm::loadHead('1');

        // inicia os eventos
        bwEvent::display();

        // mostra o debug
        bwDebug::getInstance()->show();
    }   
    
    public function getConfig()
    {
        // config
        $c = new bwConfigDB();
        $c->setPrefix('core');

        return $c;
    }

}
?>
