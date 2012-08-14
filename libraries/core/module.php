<?php

defined('BW') or die("Acesso negado!");

class bwModule
{

    function getHtml($file, $params = array())
    {
        // parametros
        $params = array_merge(array(
            'cache' => bwCore::getConfig()->getValue('cache.modulos'),
            ), $params);

        $cache_id = sha1(print_r(func_get_args(), 1));
        $cache_html = bwCache::get($cache_id, false);

        // verifica se tem o mod custumizado no template
        $file_path = bwTemplate::getInstance()->getPathHtml() . $file . '.php';

        if ($params['cache'] && $cache_html)
            return $cache;

        $html = bwUtil::execPHP($file_path, $params);

        if ($params['cache'])
            bwCache::set($cache_id, $html);

        return $html;
    }

}

?>
