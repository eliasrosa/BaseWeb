<?php

defined('BW') or die("Acesso negado!");

class bwRouter
{

    function parseUrl($url)
    {
        $url = parse_url($url);

        $url = array_merge(array(
                    'scheme' => '',
                    'host' => '',
                    'user' => '',
                    'pass' => '',
                    'path' => '',
                    'query' => '',
                    'fragment' => ''
                        ), $url);

        return $url;
    }

    function parseQuery($query_string)
    {
        parse_str($query_string, $query);

        $query = array_merge(array(
                    'com' => null,
                    'view' => null,
                    'itemid' => 0
                        ), $query);

        return $query;
    }

    function _($url, $cached = true)
    {
        if (bwCore::getConfig()->getValue('cache.url') && $cached)
        {
            $cache = bwCache::get($url, 'url');
            if ($cache)
                return $cache;
        }

        $_url = bwRouter::parseUrl($url);
        $_query = bwRouter::parseQuery($_url['query']);
        $itemid = $_query['itemid'];

        $isAdm = ($_url['path'] == 'adm.php') ? 1 : 0;

        if ($_query['itemid'])
            $alias = bwRouter::getAlias($itemid);
        elseif ($isAdm)
            $alias = array('adm');
        elseif (!$itemid && (!$isAdm && !preg_match('#^http:\/\/#', $url) && !empty($url)))
            $component = true;
        else
            return $url;

        $segments = array();

        $com = BW_PATH_COMPONENTS . DS . $_query['com'] . DS . 'router.php';
        if (bwFile::exists($com))
        {
            require_once($com);
        }

        if (bwFile::exists($com) && !$isAdm)
        {
            $function = $_query['com'] . 'buildRoute';
            $segments = $function($_query);
        }

        // se não exitir o itemid e não for adm
        if (isset($component))
        {
            $alias = array();
            $componentSegments = array();

            $componentSegments[] = 'component';
            $componentSegments[] = $_query['com'];
            $componentSegments[] = $_query['view'];
            ;

            $segments = array_merge($componentSegments, $segments);

            $token = bwRouter::getToken($_query['com'], $_query['view']);
            $_query[$token] = 1;
        }

        // se adm true
        if ($isAdm)
        {
            $segments[] = $_query['com'];

            if (isset($_query['sub']))
            {
                $segments[] = $_query['sub'];
                unset($_query['sub']);
            }

            //if(bwRequest::getVar('task', false))
            $segments[] = $_query['view'];
            //else
            //$segments[] = 'execTask';
        }

        // segments/alias
        $alias = array_merge($alias, $segments);
        $alias = join('/', $alias);

        //
        $newUrl = bwRouter::buildRoute($alias, $_query);

        // remove &amp;
        $newUrl = bwUtil::ampReplace($newUrl);

        if (bwCore::getConfig()->getValue('cache.url') && $cached)
            bwCache::set($url, $newUrl, 'url');

        return $newUrl;
    }

    function getToken($com, $view)
    {
        return sha1(sha1("/component/{$com}/{$view}.html?") . bwRequest::getToken() . '=1');
    }

    function checkToken($com, $view)
    {
        $token = bwRouter::getToken($com, $view);

        if (!bwRequest::getVar($token, false, 'get'))
            bwError::show("Token inválido!", "Acesso negado!");
    }

    function getAlias($itemid)
    {
        // pega o menu
        $menu = bwMenu::getInstance();
        $menu = $menu->getId($itemid);

        $alias = bwUtil::alias($menu['alias'], $menu['titulo']);

        $menus = array($alias);
        if ($menu['idpai'] != 0)
        {
            $pai = bwRouter::getAlias($menu['idpai']);
            $menus = array_merge($pai, $menus);
        }

        return $menus;
    }

    function getUrl()
    {
        $pageURL = 'http';
        $pageURL .= '://';

        if ($_SERVER['SERVER_PORT'] != '80')
            $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        else
            $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        return $pageURL;
    }

    function buildRoute($alias, $query)
    {
        unset($query['com'], $query['view'], $query['itemid']);

        $query = http_build_query($query);
        $query = !$query ? $query : "?$query";
        
        if(!preg_match('#^adm\/#', $alias))
            $alias .= '.html';
    
        $url = BW_URL_BASE2 . "/{$alias}{$query}";

        return $url;
    }

    function parseRoute()
    {
        $url = bwRouter::getUrl();
        $url = bwRouter::parseUrl($url);

        if (BW_URL_BASE != '')
            $alias = str_replace(BW_URL_BASE . '/', '', $url['path']);
        else
            $alias = substr($url['path'], 1);

        $alias = str_replace('.html', '', $alias);

        if ($alias == '' || $alias == 'index.php')
        {
            $alias = array();
            bwRouter::loadVarsMenus(bwCore::getConfig()->getValue('site.pagina.inicial'), $alias);
            return;
        }
        else
            $alias = explode('/', $alias);

        if ($alias[0] == 'adm')
        {
            bwRequest::setVar('com', $alias[1]);

            if (count($alias) == 3)
            {
                bwRequest::setVar('sub', $alias[1]);
                bwRequest::setVar('view', $alias[1] . '.' . $alias[2]);
            }

            if (count($alias) == 4)
            {
                bwRequest::setVar('sub', $alias[2]);
                bwRequest::setVar('view', $alias[2] . '.' . $alias[3]);
            }
        }
        elseif ($alias[0] == 'component')
        {
            bwRouter::checkToken($alias[1], $alias[2]);

            bwRequest::setVar('com', $alias[1]);
            bwRequest::setVar('view', $alias[2]);
        }
        else
        {
            // encontra o menu
            $i = -1;
            $lastMenu = false;
            do
            {
                $r = true;
                $i++;

                if (!isset($alias[$i]))
                    break;

                $menu = Doctrine_Query::create()
                                ->from('MenuItem m')
                                ->where('m.status = 1 AND alias = ?', $alias[$i])
                                ->fetchOne();

                if ($menu)
                {
                    unset($alias[$i]);
                    $lastMenu = $menu;
                }
                else
                    $r = false;
            }while ($r);


            $segments = array();
            foreach ($alias as $a)
                $segments[] = $a;


            if ($lastMenu)
            {
                $url = bwRouter::parseUrl($lastMenu->link);
                $query = bwRouter::parseQuery($url['query']);
                $itemid = $query['itemid'];

                if ($itemid > 0)
                    bwRouter::loadVarsMenus($itemid, $segments);
                else
                    bwRouter::loadVarsMenus($lastMenu->id, $segments);
            }
        }

        return;
    }

    function loadVarsMenus($itemid, $segments)
    {
        // pega o menu
        $menu = bwMenu::getInstance();
        $menu = $menu->getId($itemid);

        if (is_array($menu))
        {
            $url = bwRouter::parseUrl($menu['link']);
            $query = bwRouter::parseQuery($url['query']);
            $query['itemid'] = $itemid;

            $vars = array();
            $com = BW_PATH_COMPONENTS . DS . $query['com'] . DS . 'router.php';

            if (file_exists($com))
            {
                $function = $query['com'] . 'parseRoute';
                require_once($com);
                $vars = $function($segments);
            }

            $vars = array_merge($query, $vars);
            foreach ($vars as $k => $v)
                bwRequest::setVar($k, $v);
        }
        return;
    }
}
?>
