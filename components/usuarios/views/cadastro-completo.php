<?php
defined('BW') or die("Acesso negado!");

// adiciona js
bwHtml::js(BW_URL_COMPONENTS .'/usuarios/js/cadastro-completo.js');
bwHtml::js('/validaform/jquery.datepicker.js', true);

// carrega o api de usuario
$user = bwUsuarios::getInstance();

$redirect = bwRequest::getVar('redirect', false);
//if($redirect)
//    $user->setRedirect($redirect);

// salva os usuario
$user->saveCompleto();


// titulo
bwHtml::setTitle('Cadastro rápido');

$obr = '<span class="obr">*</span>';
?>
<h1>Cadastre-se</h1>
<p>Entre com seus dados cadastrais.</p>


<form class="tipo" action="<?= bwRouter::_("index.php?com=usuarios&view=cadastro-completo"); ?>" method="post">
        <div class="tipo-pessoa">

            <?
            if($user->getVar('tipo') == '')
                $user->setVar('tipo', 'F');
            ?>

            <label class="cpf">
                <input type="radio" value="F" name="dados[tipo]"<?= ($user->getVar('tipo') == 'F') ? ' checked="checked"' : ''?> />
                Pessoa Física
            </label>

            <label class="cnpj">
                <input type="radio" value="J" name="dados[tipo]"<?= ($user->getVar('tipo') == 'J') ? ' checked="checked"' : ''?> />
                Pessoa Jurídica
            </label>
            
        </div>
</form>

<form class="validaform completo" action="<?= bwRouter::_("index.php?com=usuarios&view=cadastro-completo"); ?>" method="post">

    <div class="dados">

        <div class="col col1">

             <h2>Dados do usuário</h2>

            <label class="nome">
                <span>Nome completo:<?= $obr; ?></span>
                <input class="txt" type="input" value="<?= $user->getVar('nome'); ?>" name="dados[nome]" title="Nome completo" />
            </label>

            <label class="email">
                <span>Seu e-mail:<?= $obr; ?></span>
                <input class="txt" type="input" value="<?= $user->getVar('email'); ?>" name="dados[email]" />
            </label>

            <label class="usuario w1">
                <span>Usuário:<?= $obr; ?></span>
                <input class="txt" type="input" value="<?= $user->getVar('user'); ?>" name="dados[user]" />
            </label>

            <label class="senha w3">
                <span>Senha:<?= $obr; ?></span>
                <input class="txt" type="password" value="" name="dados[pass]" />
            </label>

           <label class="senha2 w3">
                <span>Confime sua senha:<?= $obr; ?></span>
                <input class="txt" type="password" value="" name="dados[pass]" />
            </label>

         </div>

        <? if($user->getVar('tipo') == 'F'): ?>
        <div class="jf col col2">

            <h2>Pessoa Física</h2>

            <label class="CPF w1">
                <span>CPF:<?= $obr; ?></span>
                <input class="txt" type="input" value="<?= $user->getVar('cpf'); ?>" name="dados[cpf]" rel="cpf" />
            </label>

            <label class="RG w1">
                <span>RG:<?= $obr; ?></span>
                <input class="txt" type="input" value="<?= $user->getVar('rg'); ?>" name="dados[rg]" rel="rg" />
            </label>

            <label class="sexo">
                <span>Sexo:<?= $obr; ?></span>
                <select name="dados[sexo]" rel="text" title="Sexo">
                    <option value="">-- Selecione --</option>
                    <option value="F"<?= ($user->getVar('sexo') == 'F') ? ' selected="selected"' : ''?>>Feminino</option>
                    <option value="M"<?= ($user->getVar('sexo') == 'M') ? ' selected="selected"' : ''?>>Masculino</option>
                </select>
            </label>

            <label class="data-nasc w4">
                <span>Data de Nascimento:<?= $obr; ?></span>
                <input class="txt" type="input" value="<?= $user->getVar('dataNasc'); ?>" name="dados[dataNasc]" rel="date" />
            </label>
        </div>

        <? elseif($user->getVar('tipo') == 'J'): ?>

        <div class="pj col col2">

            <h2>Pessoa Jurídica</h2>

            <label class="razao">
                <span>Razão Social:<?= $obr; ?></span>
                <input class="txt" type="input" value="<?= $user->getVar('razaoSocial'); ?>" name="dados[razaoSocial]" />
            </label>

            <label class="cnpj w3">
                <span>CNPJ:<?= $obr; ?></span>
                <input class="txt" type="input" value="<?= $user->getVar('cnpj'); ?>" name="dados[cnpj]" rel="cnpj" />
            </label>

            <label class="ie w3">
                <span>Inscrição Estatual:<?= $obr; ?></span>
                <input class="txt" type="input" value="<?= $user->getVar('ie'); ?>" name="dados[ie]" rel="ie" />
            </label>

        </div>
        <? endif; ?>

        <div class="newsletter col col2">

            <label class="newsletter">
                <input type="checkbox" value="1" name="dados[newsletter]"<?= ($user->getVar('newsletter') == '1') ? ' checked="checked"' : ''?> />
                Receber novidades exclusivas por e-mail.
            </label>

        </div>
        
    </div>

    <br class="clearfix" />
    <p><?= $obr; ?>Campos de preenchimento obrigatório.</p>

    <input type="hidden" value="<?= $user->getVar('tipo'); ?>" name="dados[tipo]" />
    <input class="submit" type="submit" value="Criar conta!" />
    
    <?= bwHtml::createInputToken() ?>
</form>
