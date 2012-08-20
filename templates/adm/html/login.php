<?php
// carrega o título
bwHtml::setTitle('Login');

// inicia a session
$login = bwLogin::getInstance();
$template = bwTemplate::getInstance();

// finaliza qualquer sessão iniciada antes
$login->sair();

// verifica se o site esta em manuntencao
if (bwCore::getConfig()->getValue('site.offline'))
    $login->setMsg('site.offline');

// login/entrar
$user = bwRequest::getVar('user', false);
$pass = bwRequest::getVar('pass', false);

if (bwRequest::getMethod() == 'POST')
    $login->entrar($user, $pass, '/');

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

        <form class="login" action="" method="post">

            <label>Nome de usuário</label>
            <input type="input" class="txt user" name="user" title="Usuário" value="<?= bwRequest::getVar('user', ''); ?>" />

            <label>Senha</label>
            <input type="password" class="txt pass" name="pass" title="Senha" value="" />

            <input type="submit" value="Entrar" class="submit" />

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
        <span>Esqueceu sua senha, <a href="<?= bwRouter::_('/senha'); ?>">clique aqui!</a><span>
        
    </body>
</html>
