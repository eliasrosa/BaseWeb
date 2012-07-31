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
    public function _($nome, $i = array())
    {
        return bwRouter::getUrl($nome, $i);
    }

    /**
     *
     * @param type $nome
     * @param type $i
     * @return type 
     */
    function getUrl($url, $i = array())
    {
        $rotas = bwRequest::getVar('bw.core.url', array());

        if (isset($rotas[$url]) && count($i)) {
            $url = $rotas[$url]['url'];

            preg_match_all('#(:([a-zA-Z1-9-_]+))#', $url, $result);
            foreach ($result[2] as $key) {
                $url = str_replace(":{$key}", bwUtil::alias($i[$key]), $url);
            }
        }

        return BW_URL_BASE2 . $url;
    }

    /**
     * addUrl
     * 
     * @param type $url
     * @param type $name
     * @param type $conditions 
     */
    function addUrl($url, $name = NULL, $conditions = array())
    {
        $urls = bwRequest::getVar('bw.core.url', array());

        if (is_null($name)) {
            $name = $url;
        }

        $urls[$name] = array(
            'url' => $url,
            'conditions' => $conditions
        );

        bwRequest::setVar('bw.core.url', $urls);
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

        // carrega as rotas customizadas
        require_once $template->getPath() . DS . 'router.php';

        // pega url atual
        $url = new bwUrl();
        $view = str_replace(BW_URL_BASE, '', $url->getPath());
        $view = bwRequest::getVar('view', $view);

        // is home#index
        if ($view == '/') {
            bwRequest::setVar('view', bwRouter::getRoot());
            return;
        }

        $urls = bwRequest::getVar('bw.core.url', array());

        foreach ($urls as $k => $u) {
            $url = $u['url'];

            foreach ($u['conditions'] as $c => $regexp) {
                $url = str_replace($c, "($regexp)", $url);
            }

            if (preg_match_all("#$url#", $view, $result)) {

                if (count($u['conditions'])) {
                    bwRequest::setVar('view', $k);

                    unset($result[0]);
                    if (count($result)) {
                        $i = 1;
                        foreach ($u['conditions'] as $c => $regexp) {
                            bwRequest::setVar(substr($c, 1), $result[$i][0]);
                            $i++;
                        }
                    }
                } else {
                    bwRequest::setVar('view', $view);
                }

                return;
            }
        }

        //
        bwRequest::setVar('view', '/error/404');
    }

    /**
     * setRoot
     * 
     * @param type $view 
     */
    function setRoot($view)
    {
        bwRequest::setVar('bw.rootview', $view);
    }

    /**
     * getRoot
     * 
     */
    function getRoot()
    {
        return bwRequest::getVar('bw.rootview', '/index');
    }

}

?>
