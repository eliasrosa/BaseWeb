<?php

defined('BW') or die("Acesso negado!");

class bwPlugin
{

    function getAll()
    {
        $plugins = array(
            'php',
            'updateyourbrowser',
            'slimbox2',
            'analytics',
            'validaform',
            'resizeimage',
            'editinsite'
        );

        return $plugins;
    }

    function triggerEventAll($event)
    {
        foreach (bwPlugin::getAll() as $p)
            bwPlugin::triggerEvent($p, $event);
    }

    function triggerEvent($plugin, $event)
    {
        // verifica se tem o plugin custumizado no template
        $custom = bwTemplate::getInstance()->getPathHtml() . DS . 'plg_' . $plugin . DS . 'index.php';
        if (bwFile::exists($custom))
            $file = $custom;
        else
            $file = BW_PATH_PLUGINS . DS . $plugin . '.php';

        if (file_exists($file))
            require_once($file);
        else
            bwError::show("Plugin '$plugin' não foi encontrato!");


        $class = 'bwPlugin' . $plugin;
        if (class_exists($class))
            $p = new $class();
        else
            bwError::show("Class de plugin '$class' não foi encontrata!");


        $f = array($p, $event);
        if (is_callable($f))
            call_user_func_array($f, array());
    }

}

?>
