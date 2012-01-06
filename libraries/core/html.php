<?php

defined('BW') or die("Acesso negado!");

class bwHtml
{

    function js2css($file, $jsFolder = false)
    {
        bwHtml::js($file . '.js', $jsFolder);
        bwHtml::css($file . '.css', $jsFolder);
    }

    function js($file, $jsFolder = false)
    {
        if ($jsFolder)
            $file = BW_URL_JAVASCRIPTS . $file;

        $GLOBALS['bw.html.js'][$file] = $file;
    }

    function css($file, $jsFolder = false)
    {
        if ($jsFolder)
            $file = BW_URL_JAVASCRIPTS . $file;

        $GLOBALS['bw.html.css'][$file] = $file;
    }

    function setMetaData($name, $value)
    {
        $GLOBALS['bw.html.meta'][$name] = $value;
    }

    function setDescription($value, $len = 160)
    {
        $value = substr($value, 0, $len);
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

        // meta tags
        $head .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";

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

        // corrige deacordo com o formato
        $tit = str_replace('%title%', bwCore::getConfig()->getValue('site.titulo'), $tit);

        $head .= "<title>{$tit}</title>\n";

        return $head;
    }

    function getTituloMenu()
    {
        if (bwRequest::getVar('itemid', false))
        {
            $tit = Doctrine_Query::create()
                            ->from('MenuItem i')
                            ->where('i.id = ?', bwRequest::getVar('itemid'))
                            ->fetchOne();

            if (!BW_ADM)
                bwHtml::setTitle($tit->titulo);
        }else
        {
            if (!BW_ADM)
                bwHtml::setTitle(ucfirst(strtolower(bwRequest::getVar('com'))));
        }
    }


}
?>
