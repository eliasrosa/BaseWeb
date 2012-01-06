<?
defined('BW') or die("Acesso negado!");

$config = new bwConfigDB();
$json = $config->getValue('plugins.tinymce.parametros');

?>

<textarea name="conteudo" class="editor"><?= $conteudo; ?></textarea>

<script type="text/javascript">
    $(function(){
        $('textarea.editor', '#ajaxBox').tinymce($.extend(<?= $json; ?>, { 
            script_url : '<?= BW_URL_JAVASCRIPTS ?>/tiny_mce/tiny_mce.js',
            mode: 'exact',
            theme: 'advanced'
        }));
    });
</script>