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
        // inicia a session
        bwSession::init();

        // modo offline
        if (bwCore::getConfig()->getValue('site.offline') && !bwLogin::getInstance()->isLogin())
            bwUtil::redirect(BW_URL_ADM_LOGIN);

        // Carrega o template
        bwTemplate::getInstance()->load();
        
        // pega todas as rotas
        bwRouter::load();
       
        // carrega o componente para o buffer
        bwBuffer::getInstance()->loadView();

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
