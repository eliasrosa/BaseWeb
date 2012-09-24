<?php
// carrega o título
bwHtml::setTitle('Recuperar senha');

// inicia a session
$login = bwLogin::getInstance();
$template = bwTemplate::getInstance();

// finaliza qualquer sessão iniciada antes
$login->sair();


// login/entrar
$email = bwRequest::getVar('email', false);
$safeValue = bwRequest::getSafeVar('k');
$solicitacao = false;

if (bwRequest::getMethod() == 'POST') {
    $solicitacao = $login->enviarSolitacaoSenha($email, '/senha');
}

$logo = $template->getUrl() . '/img/logo.jpg';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>              

        <!-- jQuery e jQuery UI -->
        <script type="text/javascript" src="<?= BW_URL_JAVASCRIPTS ?>/jquery/jquery-1.6.min.js"></script> 

        <?= bwHtml::head(); ?>

        <link type="text/css" href="<?= $template->getUrl(); ?>/css/login.css" rel="Stylesheet" />
        <script type="text/javascript" src="<?= $template->getUrl(); ?>/js/login.js"></script> 

    </head>
    <body>  

        <img class="logo" src="<?= $logo ?>" alt="logo" />

        <?
        if (!is_null($safeValue)) {
            $login->enviarNovaSenha($safeValue);
            echo sprintf('<p>Uma nova senha foi criada e envida para %s</p>', $safeValue);
        } elseif ($solicitacao === true) {
            echo sprintf('<p>%s</p>', $login->mostrarMensagem());
        } else {
            ?>
            <form class="login" action="" method="post">

                <label>E-mail</label>
                <input type="input" class="txt user" name="email" title="E-mail" value="<?= bwRequest::getVar('email', ''); ?>" />

                <input type="submit" value="Enviar" class="submit" />

                <span class="erro">
                    <?
                    if ($login->mostrarMensagem()) {
                        echo $login->mostrarMensagem();
                    }
                    ?>
                </span>

                <div class="loading">
                    <img src="<?= BW_URL_TEMPLATE ?>/img/load1.gif" />
                </div>
            </form>
        <? } ?>
    </body>
</html>
