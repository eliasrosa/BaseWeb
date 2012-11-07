<?php

defined('BW') or die("Acesso negado!");

class bwError
{

    function show($msg = '', $tit = 'Página não encontrada!')
    {
        bwError::header404();

        echo bwUtil::acentos2Html("<h1>$tit</h1>");
        echo bwUtil::acentos2Html($msg);

        // mostra o debug
        bwDebug::getInstance()->show();

        exit();
    }

    function header404()
    {
        header('HTTP/1.0 404 Not Found');
    }

    function show404()
    {
        bwError::header404();
        bwRequest::setVar('is_show_page_404', true);
    }

}

?>
