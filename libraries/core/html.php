<?php

defined('BW') or die("Acesso negado!");

class bwHtml
{

    /**
     * Adiciona dois arquivos com o mesmo nome ao template alterando o tipo (js e css)
     * 
     * Exemplos:
     * bwHtml::js2css('contato');
     * bwHtml::js2css('plugin', true);
     * bwHtml::js2css('http://www.exemplo.com/js/contato');
     * 
     * @param string $file
     * @param boolean $jsFolder 
     */
    function js2css($file, $jsFolder = false)
    {
        bwHtml::js($file . '.js', $jsFolder);
        bwHtml::css($file . '.css', $jsFolder);
    }

    /**
     * Adiciona um arquivo JavaScript ao template
     * 
     * Exemplos:
     * bwHtml::js('contato.js');
     * bwHtml::js('plugin.js', true);
     * bwHtml::js('http://www.exemplo.com/js/contato.js');
     * 
     * @param string $file
     * @param boolean $jsFolder 
     */
    function js($file, $jsFolder = false)
    {
        if ($jsFolder) {
            $file = BW_URL_JAVASCRIPTS . $file;
        } else {
            $template = bwTemplate::getInstance();
            if (bwFile::exists($template->getPath() . DS . 'js' . DS . str_replace('/', DS, $file))) {
                $file = $template->getUrl() . '/js/' . $file;
            }
        }

        $GLOBALS['bw.html.js'][$file] = $file;
    }

    /**
     * Adiciona um arquivo CSS ao template
     * 
     * Exemplos:
     * bwHtml::css('contato.css');
     * bwHtml::css('plugin.css', true);
     * bwHtml::css('http://www.exemplo.com/css/contato.css');
     * 
     * @param string $file
     * @param boolean $jsFolder 
     */
    function css($file, $jsFolder = false)
    {
        if ($jsFolder) {
            $file = BW_URL_JAVASCRIPTS . $file;
        } else {
            $template = bwTemplate::getInstance();
            if (bwFile::exists($template->getPath() . DS . 'css' . DS . str_replace('/', DS, $file))) {
                $file = $template->getUrl() . '/css/' . $file;
            }
        }

        $GLOBALS['bw.html.css'][$file] = $file;
    }

    function setMetaData($name, $value)
    {
        $GLOBALS['bw.html.meta'][$name] = $value;
    }

    function setDescription($value, $len = 160)
    {
        bwUtil::cleanText($value);
        $value = bwUtil::truncate($value, $len);
        bwHtml::setMetaData('description', $value);
    }

    function setKeywords($value)
    {
        bwHtml::setMetaData('keywords', $value);
    }

    function createInputToken()
    {
        return '<input type="hidden" class="token" name="' . bwRequest::getToken() . '" value="1" />';
    }

    function setTitle($titulo, $autoAddSiteName = true)
    {
        if ($autoAddSiteName)
            $titulo = $titulo . ' - ' . bwCore::getConfig()->getValue('site.titulo.formato');

        $GLOBALS['bw.html.head']['title'] = $titulo;
    }

    function head()
    {
        $head = "\n";

        // setDescription default
        if (!isset($GLOBALS['bw.html.meta']['description']) || empty($GLOBALS['bw.html.meta']['description']))
            bwHtml::setDescription(bwCore::getConfig()->getValue('seo.description'));

        // setKeywords
        if (!isset($GLOBALS['bw.html.meta']['keywords']) || empty($GLOBALS['bw.html.meta']['keywords']))
            bwHtml::setKeywords(bwCore::getConfig()->getValue('seo.keywords'));

        // meta tags
        $head .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
        $head .= '<meta http-equiv="Content-Language" content="pt-br, pt" />' . "\n";
        $head .= '<meta name="rating" content="general" />' . "\n";
        $head .= '<meta name="generator" content="BaseWeb 2.0" />' . "\n";
        $head .= '<meta name="author" content="Elias da Rosa - http://www.eliasdarosa.com.br/" />' . "\n";

        $meta = isset($GLOBALS['bw.html.meta']) && is_array($GLOBALS['bw.html.meta']) ? $GLOBALS['bw.html.meta'] : array();
        foreach ($meta as $k => $v)
            $head .= '<meta name="' . $k . '" content="' . $v . '" />' . "\n";

        // adiciona os css
        $css = isset($GLOBALS['bw.html.css']) && is_array($GLOBALS['bw.html.css']) ? $GLOBALS['bw.html.css'] : array();
        foreach ($css as $f)
            $head .= '<link type="text/css" href="' . $f . '" rel="Stylesheet" />' . "\n";

        // adiciona os scripts
        $js = isset($GLOBALS['bw.html.js']) && is_array($GLOBALS['bw.html.js']) ? $GLOBALS['bw.html.js'] : array();
        foreach ($js as $f)
            $head .= '<script type="text/javascript" src="' . $f . '"></script>' . "\n";

        if (isset($GLOBALS['bw.html.head']['title']))
            $tit = $GLOBALS['bw.html.head']['title'];
        else
            $tit = bwCore::getConfig()->getValue('site.titulo.formato');

        // corrige de acordo com o formato
        $tit = str_replace('%title%', bwCore::getConfig()->getValue('site.titulo'), $tit);
        $head .= "<title>{$tit}</title>\n";

        return $head;
    }

}

?>
