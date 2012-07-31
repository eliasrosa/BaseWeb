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

        if (isset($template[1])) {
            $path = BW_PATH_TEMPLATES . DS . $template[1] . DS . 'html';
            if (bwFolder::is($path)) {
                bwRequest::setVar('template', $template[1]);
                unset($template[0], $template[1]);
                bwRequest::setVar('view', '/' . join('/', $template));
            }
        }

        $this->_name = bwRequest::getVar('template', bwCore::getConfig()->getValue('site.template'));
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
