<?php
defined('BW') or die("Acesso negado!");

$url = new bwUrl();
$user = bwUsuarios::getInstance();

// login
bwHtml::setTitle('Login');

?>
<div class="mod">
    <h1>Login</h1>
    <form class="validaform usuarios-form" action="<?= bwRouter::_("index.php?com=usuarios&view=login"); ?>" method="post">
        <label class="user">
            <span>E-mail:</span>
            <input type="input" value="" name="user" rel="text_" />
        </label>

        <label class="pass">
            <span>Senha:</span>
            <input type="password" value="" name="pass" rel="text_" />
        </label>

        <input class="submit" type="submit" value="Entrar" />
        <span class="senha">Esqueceu sua senha? <a href="<?= bwRouter::_('index.php?com=usuarios&view=recuperar-senha'); ?>">Clique aqui!</a></span>

        <input type="hidden" name="redirect" value="<?= $url->toBase64(); ?>" />
        <?= bwHtml::createInputToken() ?>
    </form>
</div>