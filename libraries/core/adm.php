<?php

defined('BW') or die("Acesso negado!");

class bwAdm
{
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    /*
     * retorna 
     * array(
     *   array(
     *      'nome' => 'nome do componente',
     *      'com' => 'pasta do componente',
     *      'link' => 'link inicial do componente',
     *      'active' => bool,
     *      'visivel' => bool,
     *   )
     * )
     */
    function getMenuPrincipal()
    {
        $r = array();
        $comAtual = bwRequest::getVar('com');
        $components = bwFolder::listarConteudo(BW_PATH_COMPONENTS, false, true, false, false);
        sort($components);
        
        foreach($components as $com)
        {
            $file = BW_PATH_COMPONENTS .DS. $com . DS . 'api.php';
            if(bwFile::exists($file))
            {
                $class = 'bw'.ucfirst(strtolower($com));
                $api = call_user_func(array($class, 'getInstance'));
            
                $r[] = array(
                    'nome' => $api->adm_nome,
                    'com' => $com,
                    'link' => bwRouter::_($api->adm_pagina_padrao),
                    'active' => ($com == $comAtual) ? true : false,
                    'visivel' => $api->adm_menu_visivel
                );
            }
        }
        
        return $r;
    }
    
    function init()
    {
        $urlAtual = new bwUrl();
        define('BW_ADM', preg_match('#^' . BW_URL_ADM . '/.*#', $urlAtual->toString()));

        bwRequest::setVar('template', BW_ADM ? bwCore::getConfig()->getValue('adm.template') : bwCore::getConfig()->getValue('site.template'));
        define('BW_URL_TEMPLATE', BW_URL_TEMPLATES . '/' . bwRequest::getVar('template'));

        // abre o adm sem efetuar login
        // bwCore::init();
        // exit();

        if (BW_ADM)
        {
            bwHtml::setTitle(bwCore::getConfig()->getValue('adm.titulo'));

            $urlEntrar = new bwUrl(BW_URL_ADM_LOGIN_FILE);
            $urlEntrar->setVar('redirect', $urlAtual->toString());

            if (!bwLogin::getInstance()->isLogin() && $urlAtual->getPath() != $urlEntrar->getPath())
                header('Location: ' . $urlEntrar->toString());
            else
            {
                $usuario = bwLogin::getInstance()->getSession();
                $dql = Doctrine_Query::create()
                                ->from('Usuario u')
                                ->innerJoin('u.Grupo g')
                                ->where('u.status = 1 AND u.id = ? AND u.user = ? AND u.pass = ? AND u.lastIp = ? AND u.lastSessionId = ? AND u.idgrupo = ?', array($usuario['id'], $usuario['user'], $usuario['pass'], $usuario['lastIp'], $usuario['lastSessionId'], $usuario['idgrupo']))
                                ->fetchOne();

                if (!$dql && $urlAtual->getPath() != $urlEntrar->getPath())
                {
                    header('Location: ' . $urlEntrar->toString());
                    exit();
                }

                // update log
                if ($dql)
                {
                    $dql->dataLastVisit = date("Y-m-d H:i:s");
                    $dql->lastIp = $_SERVER['REMOTE_ADDR'];
                    $dql->save();

                    // session
                    bwLogin::getInstance()->setSession($dql->toArray());
                }

                bwCore::init();
            }

            exit();
        }
    }

    static function msg($html, $error = false)
    {
        $class = $error ? 'erro' : 'ok';
        return '<div id="admMsg" class="'.$class.'">'.$html.'</div>';
    }

    static function loadHead($i)
    {
        if (BW_ADM)
        {
            $com = bwRequest::getVar('com');
            $view = bwRequest::getVar('view');

            $url = BW_URL_COMPONENTS . '/' . $com . '/adm';
            $path = BW_PATH_COMPONENTS . DS . $com . DS . 'adm';

            if (file_exists($path . DS . 'js' . DS . 'comum.js') && $i == '1')
                bwHtml::js($url . '/js/comum.js');

            if (file_exists($path . DS . 'css' . DS . 'style.css') && $i == '1')
                bwHtml::css($url . '/css/style.css');

            if (file_exists($path . DS . 'js' . DS . $view . '.js') && $i == '2')
                bwHtml::js($url . '/js/' . $view . '.js');

            if (file_exists($path . DS . 'css' . DS . $view . '.css') && $i == '2')
                bwHtml::css($url . '/css/' . $view . '.css');
        }
    }

    static function execTask()
    {
        $task = bwRequest::getVar('task', false);
        if (BW_ADM && $task)
        {
            if (!bwRequest::checkToken())
                die('Token inválido!');

            $tasksFile = BW_PATH_COMPONENTS . DS . bwRequest::getVar('com') . DS . 'adm' . DS . 'task.php';
            require($tasksFile);

            if (isset($redirect) && !empty($redirect))
                bwUtil::redirect($redirect);
        }
    }

    public function getImgStatus($status)
    {
        if ($status)
            return '<img class="status" src="' . BW_URL_MEDIA . '/baseweb/imagens/icos/accept.png" />';
        else
            return '<img class="status" src="' . BW_URL_MEDIA . '/baseweb/imagens/icos/remove.png" />';
    }

    public function createHtmlSubMenu($activeID, $titulo = false, $menuFile = false, $com = false)
    {
        // componente
        $com = ($com) ? $com : bwRequest::getVar('com');

        //menu file
        $menuFile = (!$menuFile) ? 'menu.php' : "menu.{$menuFile}.php";

        $file = BW_PATH_COMPONENTS . DS . $com . DS . 'adm' . DS . $menuFile;

        if (!bwFile::exists($file))
            bwError::show("Submenu não encontrado!<br/>{$file}", "Erro de script!");

        require($file);

        // active menu
        $activeID = (!$activeID) ? 0 : $activeID;

        // menu html
        $html = '<ul>';
        foreach ($menu as $id => $m)
        {
            $m = array_merge(array(
                        'url' => '',
                        'tit' => ''
                            ), $m);

            $class = '';
            $class = ($id == $activeID) ? ' active' : '';

            $html .= '<li class="item' . $class . '">';
            $html .= '<a href="' . bwRouter::_($m['url']) . '">' . $m['tit'] . '</a>';
            $html .= '</li>';
        }
        $html .= '</ul>';

        // titulo
        $titulo = (!$titulo) ? $tituloPage : $titulo;

        // div
        $menu = '<div id="submenu" class="sm01">';
        $menu .= "<h2>{$titulo}</h2>";
        $menu .= $html;
        $menu .= '</div>';

        return $menu;
    }

    
}
?>
