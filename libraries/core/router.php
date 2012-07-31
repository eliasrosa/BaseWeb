<?php

defined('BW') or die("Acesso negado!");

class bwRouter
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
        $routes = bwRouter::getRoutes();

        if (isset($routes[$url]) && count($i)) {
            $rota = $routes[$url];
            $url = $rota['url'];

            preg_match_all('#(:([a-zA-Z1-9-_]+))#', $url, $result);

            foreach ($result[2] as $k => $v) {
                $c = $rota['campos'][$k];
                $url = preg_replace("#:{$v}#", bwUtil::alias($i[$c]), $url, 1);
            }
        }

        if (!bwTemplate::getInstance()->isDefault() && $tpl_prefix == true) {
            $template = '/' . bwRequest::getVar('template');
            $url = "{$template}{$url}";
        }

        if (preg_match('#(https?|ftps?|ssh|git|rsync)://#', $url)) {
            return $url;
        } else {
            return BW_URL_BASE2 . $url;
        }
    }

    /**
     * addUrl
     * 
     * @param type $url
     * @param type $conditions 
     * @param type $type 
     */
    function addUrl($url, $campos = array(), $type = "view")
    {
        $routes = bwRouter::getRoutes();

        if (count($campos)) {
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
            'campos' => $campos,
        );

        bwRouter::setRoutes($routes);
    }

    /**
     * load
     * 
     */
    function load()
    {
        // 404 defaut
        //bwRouter::addUrl('/error/404', array(), 'static');

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

                if (count($u['campos'])) {
                    bwRequest::setVar('view', $k);

                    unset($result[0]);
                    if (count($result)) {
                        // quando não o template padrao, é adicionado um grupo
                        // a mais no regexp
                        $i = (bwTemplate::getInstance()->isDefault()) ? 1 : 2;
                        foreach ($u['campos'] as $nome) {
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
     * getRoutes
     * 
     * @return array()
     */
    function getRoutes()
    {
        return bwRequest::getVar('routes', array());
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
