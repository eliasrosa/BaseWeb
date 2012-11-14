<?php

defined('BW') or die("Acesso negado!");

class bwCore
{

    function getConexao()
    {
        return Doctrine_Manager::getInstance()->getConnection('default');
    }

    function setUtf8()
    {
        header("Content-Type: text/html; charset=utf-8", true);
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_http_input('UTF-8');
        mb_language('uni');
        mb_regex_encoding('UTF-8');
    }

    function init()
    {
        // set utf8
        bwCore::setUtf8();

        // inicia a session
        bwSession::init();

        if (defined('BW_NOT_INIT'))
            return;

        // errors
        ini_set("display_errors", bwCore::getConfig()->getValue('core.debug.showerrors'));

        // url atual
        $url = new bwUrl();
        $view = str_replace(BW_URL_BASE, '', $url->getPath());
        bwRequest::setVar('view', $view);

        // Inicia a instancia do template
        $template = bwTemplate::getInstance();

        // use www
        if (bwCore::getConfig()->getValue('core.site.usewww')) {

            if ($url->getHost() != 'localhost') {
                if (!preg_match('#^www.*$#', $url->getHost())) {
                    $new_url = str_replace($url->getHost()
                        , 'www.' . $url->getHost()
                        , $url->toString());

                    bwUtil::redirect($new_url, false, true);
                }
            }
        }

        // is offline
        if (bwCore::getConfig()->getValue('core.site.offline')) {
            if ($template->getName() != 'adm') {
                if (!bwLogin::getInstance()->isLogin()) {
                    die(bwCore::getConfig()->getValue('core.site.offline.mensagem'));
                }
            }
        }

        // pega todas as rotas
        bwRouter::load();

        // Carrega o template para o buffer
        $template->load();

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