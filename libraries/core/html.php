<?php

defined('BW') or die("Acesso negado!");

abstract class bwHtml
{

    static $data = array();

    /**
     * Adiciona dois arquivos com o mesmo nome ao template alterando o tipo (js e css)
     * 
     * Exemplos:
     * bwHtml::js2css('contato');
     * bwHtml::js2css('ie8', 'ie < 9');
     * bwHtml::js2css('chrome', 'chrome');
     * bwHtml::js2css('http://www.exemplo.com/js/contato');
     * 
     * @param string $url
     * @param string $condition 
     */
    function js2css($url, $condition = false)
    {
        bwHtml::js($url . '.js', $condition);
        bwHtml::css($url . '.css', $condition);
    }

    /**
     * Adiciona um arquivo JavaScript ao template
     * 
     * bwHtml::js('contato.js');
     * bwHtml::js('ie8.js', 'ie < 9');
     * bwHtml::js('chrome.js', 'chrome');
     * bwHtml::js('http://www.exemplo.com/js/contato.js');
     * 
     * @param string $url
     * @param string $condition 
     */
    function js($url, $condition = false)
    {
        if ($condition != false) {
            $b = new bwBrowser;

            if (!$b->isBrowser($condition))
                return;
        }

        $template = bwTemplate::getInstance();
        if (bwFile::exists($template->getPath() . DS . 'js' . DS . str_replace('/', DS, $url))) {
            $url = $template->getUrl() . '/js/' . $url;
        }

        $head = "<script type=\"text/javascript\" src=\"$url\"></script>";
        bwHtml::addHead('js', $head);
    }

    /**
     * Adiciona um arquivo CSS ao template
     * 
     * bwHtml::css('contato.css');
     * bwHtml::css('ie8.css', 'ie < 9');
     * bwHtml::css('chrome.css', 'chrome');
     * bwHtml::css('http://www.exemplo.com/css/contato.css');
     * 
     * @param string $url
     * @param string $condition  
     * @param string $rel = 'Stylesheet'
     */
    function css($url, $condition = false, $rel = 'Stylesheet')
    {
        if ($condition != false) {
            $b = new bwBrowser;

            if (!$b->isBrowser($condition))
                return;
        }

        $template = bwTemplate::getInstance();
        if (bwFile::exists($template->getPath() . DS . 'css' . DS . str_replace('/', DS, $url))) {
            $url = $template->getUrl() . '/css/' . $url;
        }

        $head = "<link type=\"text/css\" href=\"$url\" rel=\"$rel\" />";
        bwHtml::addHead('css', $head);
    }

    function addMeta($name, $content, $type_name = 'name')
    {
        $head = "<meta $type_name=\"$name\" content=\"$content\" />";
        bwHtml::addHead('meta', $head);
    }

    function setDescription($value, $len = 160)
    {
        $value = bwUtil::truncate(bwUtil::cleanText($value), $len);
        $head = "<meta name=\"description\" content=\"$value\" />";
        bwHtml::addHead('description', $head, true);
    }

    function setKeywords($value)
    {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        $head = "<meta name=\"keywords\" content=\"$value\" />";
        bwHtml::addHead('keywords', $head, true);
    }

    function setTitle($title_page, $add_site_name = true)
    {
        if ($add_site_name) {
            $site_name = bwCore::getConfig()->getValue('site.titulo');
            $title_format = bwCore::getConfig()->getValue('site.titulo.formato');
            $title_format = str_replace('%title_page%', $title_page, $title_format);
            $title_page = str_replace('%site_name%', $site_name, $title_format);
        }

        bwHtml::addHead('title', "<title>$title_page</title>", true);

        return;
    }

    function setCanonical($router)
    {
        $router = bwRouter::_($router);
        $head = "<link rel=\"canonical\" href=\"$router\" />";
        bwHtml::addHead('canonical', $head, true);
    }

    function addLink($rel, $href)
    {
        $head = "<link rel=\"$rel\" href=\"$href\" />";
        bwHtml::addHead('link', $head);
    }

    function addCutom($head)
    {
        bwHtml::addHead('custom', $head);
    }

    function createInputToken()
    {
        return '<input type="hidden" class="token" name="' . bwRequest::getToken() . '" value="1" />';
    }

    function head()
    {
        //
        bwHtml::addMeta('rating', 'general');
        bwHtml::addMeta('generator', 'BW - PHP Framework | http://github.com/eliasrosa/baseweb');
        bwHtml::addMeta('Content-Language', 'pt-br', 'http-equiv');
        bwHtml::addMeta('Content-Type', 'text/html; charset=utf-8', 'http-equiv');

        //
        if (!bwHtml::$data['title']) {
            bwHtml::setTitle(bwCore::getConfig()->getValue('site.titulo'), false);
        }

        //
        if (!isset(bwHtml::$data['keywords'])) {
            bwHtml::setKeywords(bwCore::getConfig()->getValue('seo.keywords'));
        }

        //
        if (!isset(bwHtml::$data['description'])) {
            bwHtml::setDescription(bwCore::getConfig()->getValue('seo.description'));
        }

        //
        $head = "\n\t\t<!-- bw include head -->\n";
        foreach (bwHtml::$data as $types) {

            if (is_array($types)) {
                foreach ($types as $h) {
                    $head .= "\t\t$h\n";
                }
            } else {
                $head .= "\t\t$types\n";
            }
        }
        
        return $head;
    }

    /**
     * 
     * @param type $tipo
     * @param type $head
     */
    function addHead($type, $head, $unique = false)
    {
        if ($unique) {
            bwHtml::$data[$type] = $head;
            return;
        }

        $sha1 = sha1($head);
        bwHtml::$data[$type][$sha1] = $head;
        return;
    }

}
