<?php
defined('BW') or die("Acesso negado!");

// adiciona js
bwHtml::js(BW_URL_COMPONENTS .'/usuarios/js/cadastro.js');

// redirect
$redirect = bwRequest::getVar('r', false);

// carrega o api de usuario
$user = bwUsuarios::getInstance();

// titulo
bwHtml::setTitle('Cadastro rápido');
?>
<div class="mod">
    <h1>Não sou cadastrado</h1>
    <form class="validaform" action="<?= bwRouter::_("index.php?com=usuarios&view=cadastro-completo"); ?>" method="post">
        <label class="email"><span>E-mail:</span><input type="input" value="" name="dados[email]" rel="email_" title="E-mail" /></label>
        <label class="cep"><span>CEP:</span><input type="input" value="" name="dados[cep]" rel="cep_" title="CEP" /></label>
        <input class="submit" type="submit" value="Criar conta" />
        <input class="redirect" type="hidden" name="redirect" value="<?= $redirect; ?>" />
        <input class="" type="hidden" name="dados[tipo]" value="F" />
        <?= bwHtml::createInputToken() ?>
    </form>
</div>
