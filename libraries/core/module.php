<?php

defined('BW') or die("Acesso negado!");

class bwModule
{

    function getHtml($module, $params = array())
    {
        $html = '';
        $itemid = bwRequest::getVar('itemid');

        // parametros
        $params = array_merge(array(
                    'cache' => bwCore::getConfig()->getValue('cache.modulos'),
                    'visualizar' => array(),
                    'naoVisualizar' => array()
                        ), $params);


        // se o parametro visualizar estiver vazio
        // o modulo será visivel para todos os itens de menu
        if (count($params['visualizar']))
        {
            if (!in_array($itemid, $params['visualizar']))
                return '';
        }

        if (in_array($itemid, $params['naoVisualizar']))
            return '';

        $cacheID = "{$module}::" . print_r($params, 1);
        $cache = bwCache::get($cacheID, 'mod');

        // verifica se tem o mod custumizado no template
        $custom = bwTemplate::getInstance()->getPathHtml() . DS . 'mod_' . $module . DS . 'index.php';
        if (bwFile::exists($custom))
        {
            $path = bwTemplate::getInstance()->getPathHtml() . DS . 'mod_' . $module;
            $url = bwTemplate::getInstance()->getUrlHtml() . '/mod_' . $module;
        }
        else
        {
            $path = BW_PATH_MODULOS . DS . $module;
            $url = BW_URL_MODULOS . '/' . $module;
        }
        if (bwFile::exists($path . DS . 'config.php'))
            require($path . DS . 'config.php');

        if (bwFile::exists($path . DS . 'mod.css'))
            bwHtml::css("$url/mod.css");

        if (bwFile::exists($path . DS . 'mod.js'))
            bwHtml::js("$url/mod.js");

        if ($params['cache'] && $cache)
            return $cache;

        // ativa o buffer
        ob_start();

        // abre o módulo
        $file = $path . DS . 'index.php';
        if (bwFile::exists($file))
            require($file);
        else
            bwError::show("Modúlo '$module' não foi encontrado!");

        $html = ob_get_clean();

        if ($params['cache'])
            bwCache::set($cacheID, $html, 'mod');

        return $html;
    }

    function add($pos, $mod, $params = array())
    {
        bwBuffer::getInstance()->addModule($pos, $mod, $params);
    }
}
?>
