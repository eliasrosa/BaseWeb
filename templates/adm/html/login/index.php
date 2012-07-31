<?php
define('BW', 1);
define('DS', DIRECTORY_SEPARATOR);
define('BW_PATH', dirname(__FILE__) . DS . '..' . DS . '..');

@require_once(BW_PATH . DS . 'config.php');
@require_once(BW_PATH . DS . 'libraries' . DS . 'defines.php');
@require(BW_PATH_LIBRARIES . DS . 'core' . DS . 'loader.php');

// auto load
bwLoader::import('doctrine.doctrine');

// inicia o auto load
spl_autoload_register('bwLoader::autoload');

// arquivos importantes
bwLoader::import('core.functions');
bwLoader::import('core.conexao');

// carrega o título
bwHtml::setTitle('Login');

// inicia a session
$login = bwLogin::getInstance();

// finaliza qualquer sessão iniciada antes
$login->sair();

// verifica se o site esta em manuntencao
if (bwCore::getConfig()->getValue('site.offline'))
    $login->setMsg('site.offline');

// login/entrar
$user = bwRequest::getVar('user', false);
$pass = bwRequest::getVar('pass', false);

if (bwRequest::getMethod() == 'POST')
    $login->entrar($user, $pass, 'adm.php?com=sistema&view=inicio');

// carrega o logotipo
if (bwFile::exists(bwTemplate::getInstance()->getPath() . DS . 'adm-logo.jpg'))
    $logo = bwTemplate::getInstance()->getUrl() . '/adm-logo.jpg';
else
    $logo = BW_URL_LOGO;

$logo = bwUtil::resizeImage("[image width='300' height='150' src='{$logo}']");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>              

        <!-- jQuery e jQuery UI -->
        <script type="text/javascript" src="<?= BW_URL_JAVASCRIPTS ?>/jquery/jquery-1.6.min.js"></script> 

        <?= bwHtml::head(); ?>

        <link type="text/css" href="<?= BW_URL_ADM_LOGIN ?>/css/style.css" rel="Stylesheet" />
        <script type="text/javascript" src="<?= BW_URL_ADM_LOGIN ?>/js/login.js"></script> 

    </head>
    <body>  

        <img class="logo" src="<?= $logo ?>" />

        <? if ($login->mostrarMensagem()): ?>
            <p class="erro"><?= $login->mostrarMensagem(); ?></p>
        <? endif; ?>
        <form class="login" action="<?= BW_URL_ADM_LOGIN_FILE ?>" method="post">
            <label>Nome de usuário</label>
            <input type="input" class="txt user" name="user" title="Usuário" value="<?= bwRequest::getVar('user', ''); ?>" />

            <label>Senha</label>
            <input type="password" class="txt pass" name="pass" title="Senha" value="" />

            <input type="submit" value="Entrar" />              
            <input type="hidden" name="<?= bwRequest::getToken(); ?>" value="1" />              
        </form>

        <?
        if (bwCore::getConfig()->getValue('debug.status')) {
            echo base64_decode(bwRequest::getVar('redirect', NULL));
            echo '<div style="text-align: left;">';
            bwDebug::show();
            echo '</div>';
        }
        ?>
    </body>
</html>





