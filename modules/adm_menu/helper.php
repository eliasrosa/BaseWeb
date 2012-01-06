<?php

defined('BW') or die("Acesso Restrito");

class bwModuleAdmMenu
{

    function show()
    {
        global $menus;
        
        $com = bwRequest::getVar('com');

        $file = BW_PATH_COMPONENTS . DS . $com . DS . 'adm' . DS . 'menu.php';
        if (file_exists($file))
            require($file);

        $htm = '';

        foreach ($menu as $m)
        {
            $m = array_merge(array(
                        'url' => '',
                        'tit' => ''
                            ), $m);
			
			$u = new bwUrl();
            $u2 = new bwUrl(bwRouter::_($m['url']));
							
            $class = ($u->toString() == $u2->toString()) ? ' active' : '';
            $htm .= '<div class="item'. $class. '"><a href="' . $u2->toString() . '">' . $m['tit'] . '</a></div>';

            $class = '';
        }

        echo $htm;
    }

}

?>