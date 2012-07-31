<?php

defined('BW') or die("Acesso negado!");

class bwTemplate extends bwObject
{

    //
        private $_name, $_path, $_url;

    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    // carrega infornações básicas do template
    function __construct($template = NULL)
    {
        $this->setName($template);
        $this->setPath();
        $this->setUrl();
    }

    private function setName($template = NULL)
    {
        if (!is_null($template)) {
            $this->_name = $template;
            return;
        }

        $url = new bwUrl();
        $view = str_replace(BW_URL_BASE, '', $url->getPath());
        $template = explode('/', $view);
        $tpl_name = $template[1];

        if (isset($template[1])) {
            $path = BW_PATH_TEMPLATES . DS . $tpl_name . DS . 'html';
            if (bwFolder::is($path)) {
                bwRequest::setVar('template', $tpl_name);
            }
        }

        $this->_name = bwRequest::getVar('template', $this->getDefault());
    }

    private function setPath()
    {
        $this->_path = BW_PATH_TEMPLATES . DS . $this->getName();
    }

    private function setUrl()
    {
        $this->_url = BW_URL_BASE2 . '/templates/' . $this->getName();
    }

    function getName()
    {
        return $this->_name;
    }

    function getPath()
    {
        return $this->_path;
    }

    function getPathHtml()
    {
        return $this->_path . DS . 'html';
    }

    function getUrl()
    {
        return $this->_url;
    }
    
    function isDefault()
    {
        $t1 = $this->getDefault();
        $t2 = bwRequest::getVar('template', $t1);
        
        return ($t1 == $t2);
    }

    function getDefault()
    {
        return bwCore::getConfig()->getValue('site.template');
    }

    function getUrlHtml()
    {
        return $this->_url . '/html';
    }

    function load()
    {
        // ativa o buffer
        ob_start();

        // abre o arquivo
        require_once($this->getPath() . DS . 'index.php');

        // captura o buffer
        $buffer = bwBuffer::getInstance();
        $buffer->setHtml(ob_get_clean());
    }

}

?>
