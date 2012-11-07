<?php

defined('BW') or die("Acesso negado!");

class bwHtml
{

    /**
     * Adiciona dois arquivos com o mesmo nome ao template alterando o tipo (js e css)
     * 
     * Exemplos:
     * bwHtml::js2css('contato');
     * bwHtml::js2css('ie8', 'ie < 9');
     * bwHtml::js2css('chrome', 'chrome');
     * bwHtml::js2css('http://www.exemplo.com/js/contato');
     * 
     * @param string $file
     * @param boolean $condition 
     */
    function js2css($file, $condition = false)
    {
        bwHtml::js($file . '.js', $condition);
        bwHtml::css($file . '.css', $condition);
    }

    /**
     * Adiciona um arquivo JavaScript ao template
     * 
     * bwHtml::js('contato.js');
     * bwHtml::js('ie8.js', 'ie < 9');
     * bwHtml::js('chrome.js', 'chrome');
     * bwHtml::js('http://www.exemplo.com/js/contato.js');
     * 
     * @param string $file
     * @param boolean $condition 
     */
    function js($file, $condition = false)
    {
        if ($condition != false) {
            $b = new bwBrowser;

            if (!$b->isBrowser($condition))
                return;
        }

        $template = bwTemplate::getInstance();
        if (bwFile::exists($template->getPath() . DS . 'js' . DS . str_replace('/', DS, $file))) {
            $file = $template->getUrl() . '/js/' . $file;
        }

        $GLOBALS['bw.html.js'][$file] = $file;
    }

    /**
     * Adiciona um arquivo CSS ao template
     * 
     * bwHtml::css('contato.css');
     * bwHtml::css('ie8.css', 'ie < 9');
     * bwHtml::css('chrome.css', 'chrome');
     * bwHtml::css('http://www.exemplo.com/css/contato.css');
     * 
     * @param string $file
     * @param $condition bwBrowser 
     */
    function css($file, $condition = false)
    {
        if ($condition != false) {
            $b = new bwBrowser;

            if (!$b->isBrowser($condition))
                return;
        }

        $template = bwTemplate::getInstance();
        if (bwFile::exists($template->getPath() . DS . 'css' . DS . str_replace('/', DS, $file))) {
            $file = $template->getUrl() . '/css/' . $file;
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
        $head .= '<meta name="generator" content="BaseWeb | http://github.com/eliasrosa/baseweb" />' . "\n";

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

        
        // adiciona os link
        $links = isset($GLOBALS['bw.html.link']) && is_array($GLOBALS['bw.html.link']) ? $GLOBALS['bw.html.link'] : array();
        foreach ($links as $f)
            $head .= $f."\n";

        
        // corrige de acordo com o formato
        $tit = str_replace('%title%', bwCore::getConfig()->getValue('site.titulo'), $tit);
        $head .= "<title>{$tit}</title>\n";

        return $head;
    }

}

?>
