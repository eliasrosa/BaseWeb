<?php

defined('BW') or die("Acesso negado!");

class bwTemplate extends bwObject
{

    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    // nome da pasta
    private $nome;
    // path
    private $path;
    // path
    private $url;

    // carrega infornações básicas do template
    function __construct()
    {
        $this->setNome();
        $this->setPath();
        $this->setUrl();
    }

    private function setNome()
    {
        $this->nome = bwRequest::getVar('template', bwCore::getConfig()->getValue('site.template'));
    }

    private function setPath()
    {
        $this->path = BW_PATH_TEMPLATES . DS . $this->getNome();
    }

    private function setUrl()
    {
        $this->url = BW_URL_BASE2 . '/templates/' . $this->getNome();
    }

    function getNome()
    {
        return $this->nome;
    }

    function getPath()
    {
        return $this->path;
    }

    function getPathHtml()
    {
        return $this->path . DS . 'html';
    }

    function getUrl()
    {
        return $this->url;
    }

    function getUrlHtml()
    {
        return $this->url . '/html';
    }

    function carregar()
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
