<?php

defined('BW') or die("Acesso negado!");

class bwAdm
{

    function init($url_login)
    {
        // seta como ADM
        define('BW_ADM', true);
        
        // login de administrador somente
        bwLogin::getInstance()->restrito(true, $url_login);
    }

    static function msg($html, $error = false)
    {
        $class = $error ? 'erro' : 'ok';
        return '<div id="admMsg" class="' . $class . '">' . $html . '</div>';
    }

    public function getImgStatus($status)
    {
        if ($status)
            return '<img class="status" src="' . BW_URL_MEDIA . '/accept.png" />';
        else
            return '<img class="status" src="' . BW_URL_MEDIA . '/remove.png" />';
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
        foreach ($menu as $id => $m) {
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
