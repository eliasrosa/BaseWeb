<?php

defined('BW') or die("Acesso negado!");

//
require BW_PATH_LIBRARIES . DS . 'smarty' . DS . 'Smarty.class.php';

class bwSmarty extends Smarty
{

    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    function __construct()
    {
        //
        parent::__construct();

        //
        $this->_addPlugins();

        //
        $this->_configure();
    }

    private function _addPlugins()
    {
        $this->addPluginsDir(BW_PATH_PLUGINS . DS . 'smarty');
    }

    private function _configure()
    {
        //
        $path_cache = BW_PATH_CACHE . DS . 'smarty';
        
        //
        $this->setCompileDir($path_cache . DS . 'templates_c');
        $this->setCacheDir($path_cache . DS . 'cache');

    }

}

?>
