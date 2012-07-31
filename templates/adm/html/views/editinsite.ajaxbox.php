<?
defined('BW') or die("Acesso negado!");
$file = bwRequest::getVar('file');
$tipo = bwRequest::getVar('tipo');

$conteudo = bwEditInSite::getConteudo($file);

if(!bwEditInSite::getPath($file))
{
    echo '<p>Arquivo não encontrado!</p>';
    echo '<div class="rodape-toolbar"><a class="btn close" href="javascript:$.ajaxBox.Fechar();">FECHAR</a></div>';
    echo "<script type=\"text/javascript\">$(function(){  $('a.btn', '#ajaxBox .rodape-toolbar').button(); }); </script>";
    exit();
}

$url = bwRouter::_('/adm/editinsite');
?>

<form action="<?= $url; ?>" class="html" method="post">
    <input type="hidden" name="task" value="salvarInSite" />
    <input type="hidden" name="file" value="<?= $file ?>" />
    <input type="hidden" name="tipo" value="<?= $tipo ?>" />
    <?= bwHtml::createInputToken(); ?>
    <h1>Editar</h1>
    <? require(dirname(__FILE__) .DS. 'editinsite' .DS. $tipo .'.php'); ?>
</form>

<div class="rodape-toolbar">
    <a class="btn save" href="javascript:void(0);">SALVAR</a>
    <a class="btn close" href="javascript:$.ajaxBox.Fechar();">CANCELAR</a>
</div>

<script type="text/javascript">
    $(function(){
        $('a.btn', '#ajaxBox .rodape-toolbar').button();

        $('a.save', '#ajaxBox .rodape-toolbar').click(function(){
            $('form', '#ajaxBox').submit();
            $('#ajaxBox .rodape-toolbar').html('Salvando...');
        });
    });
</script>
<? exit(); ?>