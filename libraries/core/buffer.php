<?php
defined('BW') or die("Acesso negado!");

class bwBuffer extends bwObject
{
    //
    private $html = null;
    
    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    public function setHtml($html)
    {
        $this->html = $html;
    }

    public function getHtml()
    {
        return $this->html;
    }

    function loadView()
    {
        $html = false;
        $view = bwRequest::getVar('view');
        $template = bwRequest::getVar('template', bwCore::getConfig()->getValue('site.template'));
        $path = BW_PATH_TEMPLATES . DS . $template . DS . 'html';

        
        //
        if (substr($view, -1) == '/') {
            $file = $path . $view . DS . 'index.php';
        } else {
            $file = $path . $view . '.php';
        }

       
        //
        if (bwFile::exists($file)) {
            $html = bwUtil::execPHP($file);
        } else {
            // caso for uma pasta / redireciona
            $folder = $path . $view;
            
            if (bwFolder::is($folder) && (substr($folder, -1) != '/')) {
                header('HTTP/1.1 301 Moved Permanently');
                header(sprintf('Location: %s%s/', BW_URL_BASE, $view));
                exit();
            }
        }

        //
        if ($html == false) {
            bwError::header404();
            $file = $path . DS . 'error/404.php';
            $html = bwUtil::execPHP($file);
        }

        $this->html = str_replace('{BW VIEW}', $html, $this->html);
    }

    function getHtmlHead()
    {
        $this->html = str_replace('{BW HEAD}', bwHtml::head(), $this->html);
    }

}
