<?
bwHtml::js(BW_URL_JAVASCRIPTS . '/prettyPhoto/jquery.prettyPhoto.js');
bwHtml::css(BW_URL_JAVASCRIPTS . '/prettyPhoto/css/prettyPhoto.css');

bwHtml::js(BW_URL_JAVASCRIPTS . '/html5_upload.js');
bwHtml::js('scrollFixed.js');

bwHtml::css('galeria.css');
bwHtml::js('galeria.js');

$keysafe = bwRequest::getVar('keysafe');
if (!bwRequest::getSafeVar('keysafe'))
    die('Key inválida!');

$b = new bwBrowser();
if ($b->getBrowser() == 'ie') {
    echo '<div class="aviso erro ie">';
    echo '<strong>Navegador não recomendado</strong><br /><br />';
    echo '<p>Este navegador não tem suporte a alguns recursos do HTML5.</p>';
    echo '</div>';
}
?>


<div id="upload">
    
    <h1>Galeria de imagens</h1>

    <input type="file" multiple="multiple" />
    <div class="name">Selecione uma ou mais imagens!</div>
    <div class="status"></div>
    <div class="bar"><div></div></div>
    <div class="toolbar">
        <a href="javascript:" class="limpar-todos">Limpar tudo</a> | 
        <a href="javascript:" class="limpar-avisos">Limpar avisos</a> | 
        <a href="javascript:" class="limpar-erros">Limpar erros</a>
    </div>
    
    <div id="log"></div>
    
</div>

<div id="imagens" data-keysafe="<?= $keysafe; ?>">
    <form>
        
    </form>
    <br class="clearfix" />
</div>


<br class="clearfix" />

