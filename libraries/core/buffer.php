<?php

defined('BW') or die("Acesso negado!");

class bwBuffer extends bwObject
{
    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
 
    private $modules = array();
    private $html = null;

    function addModule($pos, $mod, $params = array())
    {
        $this->modules[$pos][] = array(
            'mod' => $mod,
            'params' => $params
        );
    }

    function getModules()
    {
        return $this->modules;
    }

    function setHtml($html)
    {
        $this->html = $html;
    }

    function getHtml()
    {
        return $this->html;
    }

    // carrega o componente para o buffer
    function carregarHtmlComponente()
    {
        $com = bwRequest::getVar('com');
        $view = bwRequest::getVar('view');
        $itemid = bwRequest::getVar('itemid', 0);

        $menu = bwMenu::getInstance();
        $params = $menu->getParams($itemid);

        $com = bwComponent::load($com, $view, $params);
        $this->html = str_replace('{BW COMPONENT}', $com, $this->html);
    }

    // carrega os todos os modulos para buffer
    function carregarHtmlModulos()
    {
        $file = bwTemplate::getInstance()->getPath() . DS . 'modules.php';

        if (bwFile::exists($file))
            require_once $file;

        preg_match_all("#{BW MODULES (.*)}#i", $this->html, $resultado);

        $pos = array();
        for ($i = 0; $i < count($resultado[1]); $i++)
            $pos[strtolower($resultado[1][$i])] = $resultado[0][$i];

        foreach ($pos as $k => $v)
        {
            $html = '';
            if (isset($this->modules[$k]) && count($this->modules[$k]))
            {
                foreach ($this->modules[$k] as $m)
                {
                    $html .= bwModule::getHtml($m['mod'], $m['params']);
                }
            }
            $this->html = str_replace($v, $html, $this->html);
        }
    }

    function getHtmlHead()
    {
        $this->html = str_replace('{BW HEAD}', bwHtml::head(), $this->html);
    }
}
?>
