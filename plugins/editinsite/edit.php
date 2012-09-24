<?php
define('BW_NOT_INIT', true);
require '../../index.php';

$login = bwLogin::getInstance();

if (!$login->isLogin()) {
    die('Você não está logado!');
}

$file = bwRequest::getSafeVar('key');
$path = BW_PATH_TEMPLATES . DS . $file;
$tipo = bwRequest::getVar('tipo');

if (!bwFile::exists($path) || !bwFile::is($path)) {
    die("Arquivo não encontrado!");
}


if (bwRequest::getMethod() == 'POST') {

    //
    $dados = bwRequest::getVar('txt');

    //
    if ($tipo == 'txt') {
        $dados = str_replace("\n\r", ' ', $dados);
        $height = 190;
    }

    //
    bwEditInSite::setConteudo($file, $dados);

    //
    die('Seus dados foram salvos com sucesso!');
}


//
$height = ($tipo == 'txt') ? 220 : 310;
$txt = bwEditInSite::getConteudo($file);
?>
<form action="<?= BW_URL_PLUGINS; ?>/editinsite/edit.php">
    <h3 style="">Editar</h3>
    <input type="hidden" value="<?= bwRequest::getVar('key'); ?>" name="key" />
    <textarea style="height: <?= $height; ?>px;" name="txt"><?= $txt; ?></textarea>
</form>
