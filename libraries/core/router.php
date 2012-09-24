<?php

defined('BW') or die("Acesso negado!");

abstract class bwRouter
{

    /**
     *  Alias bwRouter::getUrl
     * 
     * @param type $nome
     * @param type $i 
     */
    public function _($nome, $tpl_prefix = true, $i = array())
    {
        return bwRouter::getUrl($nome, $tpl_prefix, $i);
    }

    /**
     *
     * @param type $nome
     * @param type $i
     * @return type 
     */
    function getUrl($url, $tpl_prefix = true, $i = array())
    {
        // is absolute
        if (preg_match('#^(https?|ftps?|ssh|git|rsync)://#', $url)) {
            return $url;
        }

        $routes = bwRouter::getRoutes();

        if (isset($routes[$url]) && count($i)) {
            $route = $routes[$url];
            $url = $route['url'];

            preg_match_all('#(:([a-zA-Z1-9-_]+))#', $url, $result);

            foreach ($result[2] as $k => $v) {
                $c = $route['fields'][$k];
                $url = preg_replace("#:{$v}#", bwUtil::alias($i[$c]), $url, 1);
            }
        }

        if (!bwTemplate::getInstance()->isDefault() && $tpl_prefix == true) {
            $template = '/' . bwRequest::getVar('template');
            $url = "{$template}{$url}";
        }

        return BW_URL_BASE2 . $url;
    }

    /**
     * Retorna uma única rota
     * 
     * @param string $router
     * @param string $type_return 'array' || 'object'
     * @return array|object
     */
    function getRoute($router = NULL, $type_return = 'array')
    {
        if (is_null($router)) {
            $router = bwRouter::getRealView();
        }

        $r = bwRouter::getRoutes($router);
        if ($type_return == 'array') {
            return $r[$router];
        }

        if ($type_return == 'object') {
            return bwUtil::array2object($r[$router]);
        }
    }

    function getRealView()
    {
        //
        $view = bwRequest::getVar('view');

        //
        if (!bwTemplate::getInstance()->isDefault()) {
            $template = strlen(bwRequest::getVar('template'));
            $view = substr($view, $template + 1);
        }

        return $view;
    }

    /**
     * addUrl
     * 
     * @param string $url
     * @param array $params 
     * 
     * @return string $url
     */
    function addUrl($url, $params = array())
    {
        // sobreescreve os parametros
        extract(array_merge(array(
                'sitemap' => true,
                'fields' => array(),
                'type' => 'view',
                'title' => '',
                'keywords' => '',
                'description' => '',
                'alias' => '',
                'redirect' => '',
                ), $params));

        $routes = bwRouter::getRoutes();

        if (count($fields)) {
            $name = strstr($url, '/:', true);
        } else {
            $name = $url;
        }

        $regexp = str_replace(':alias', '([\w\d-]+)', $url);
        $regexp = str_replace(':id', '(\d+)', $regexp);

        //
        if (!bwTemplate::getInstance()->isDefault()) {
            $template = '/' . bwRequest::getVar('template');
            $regexp = "({$template})?{$regexp}";
        }

        $routes[$name] = array(
            'type' => $type,
            'url' => $url,
            'regexp' => "#^{$regexp}/?$#",
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description,
            'redirect' => $redirect,
            'fields' => $fields,
            'sitemap' => $sitemap,
        );

        if (isset($skip_constraint))
            $routes[$name]['skip_constraint'] = $skip_constraint;

        //
        bwRouter::setRoutes($routes);

        if (is_string($alias) && $alias != '') {
            $alias = array($alias);
        }

        //
        if (is_array($alias) && count($alias)) {

            foreach ($alias as $a) {
                bwRouter::addUrl($a, array(
                    'type' => 'header301',
                    'redirect' => $url
                ));
            }
        }
    }

    /**
     * load
     * 
     */
    function load()
    {
        // carrega todas as rotas 
        $components = bwFolder::listarConteudo(BW_PATH_COMPONENTS, false, true, false, false);
        foreach ($components as $com) {
            $file = BW_PATH_COMPONENTS . DS . $com . DS . 'router.php';
            if (bwFile::exists($file)) {
                require $file;
            }
        }

        // template
        $template = bwTemplate::getInstance();
        $template_name = $template->getName();

        // carrega as rotas customizadas
        require_once $template->getPath() . DS . 'router.php';

        // carrega view      
        $view = bwRequest::getVar('view');

        // is home#index
        if ($view == '/' || $view == '' || (!$template->isDefault() && preg_match("#^/{$template_name}/?$#", $view))) {
            bwRequest::setVar('view', bwRouter::getRoot());
            return;
        }

        //
        $rotas = bwRouter::getRoutes();
        foreach ($rotas as $k => $u) {

            if (preg_match_all($u['regexp'], $view, $result)) {

                if (count($u['fields'])) {
                    bwRequest::setVar('view', $k);

                    unset($result[0]);
                    if (count($result)) {
                        // quando não o template padrao, é adicionado um grupo
                        // a mais no regexp
                        $i = (bwTemplate::getInstance()->isDefault()) ? 1 : 2;
                        foreach ($u['fields'] as $nome) {
                            bwRequest::setVar($nome, $result[$i][0]);
                            $i++;
                        }
                    }
                } else {
                    bwRequest::setVar('view', $view);
                }

                return;
            }
        }

        bwError::header404();
        bwDebug::addHeader('Router not found!(' . $view . ')');
        bwRequest::setVar('view', '/error/404');
    }

    /**
     * setRoot
     * 
     * @param string $view 
     */
    function setRoot($view)
    {
        bwRequest::setVar('view-root', $view);
    }

    /**
     * getRoot
     * 
     * @return string 
     */
    function getRoot()
    {
        return bwRequest::getVar('view-root', '/index');
    }

    /**
     * Retorna todas as rotas correspodentes a expressão regular
     * 
     * @param string $pattern
     * @return array()
     */
    function getRoutes($filter = '.')
    {
        $routers = array();
        foreach (bwRequest::getVar('routes', array()) as $subject => $v) {
            $pattern = sprintf("#^%s#", $filter);
            if (preg_match($pattern, $subject)) {
                $routers[$subject] = $v;
            }
        }

        return $routers;
    }

    /**
     * setRoutes
     *
     * @param array $routes 
     */
    function setRoutes($routes)
    {
        bwRequest::setVar('routes', $routes);
    }

}

?>
