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
        $template = bwRequest::getVar('template', bwTemplate::getInstance()->getDefault());
        $path = BW_PATH_TEMPLATES . DS . $template . DS . 'html';
        $template_prefix = '';
        $routes = bwRouter::getRoutes();

        // is other template
        if (!bwTemplate::getInstance()->isDefault()) {

            //
            $template_prefix = '/' . bwRequest::getVar('template');

            // remove o template do view
            $view = preg_replace("#^{$template_prefix}#", '', $view, 1);

            //
            if ($view == '/' || $view == '') {
                $view = $template_prefix;
            }

            //is adm
            if (defined('BW_ADM')) {
                $com = explode('/', $view);
                if (isset($com[1]) && bwFolder::is(BW_PATH_COMPONENTS . DS . $com[1])) {
                    
                    bwRequest::setVar('com', $com[1]);

                    $path = BW_PATH_COMPONENTS . DS . $com[1] . DS . 'adm' . DS . 'views';
                    $view = str_replace("/{$com[1]}", '', $view);

                    if ($view == '') {
                        $view = '/';
                    }
                }
            }
        }

        // is folder or file
        if (substr($view, -1) == '/') {
            $file = $path . $view . 'index.php';
        } else {
            $file = $path . $view . '.php';
        }

        // is exist file
        if (bwFile::exists($file)) {
            $html = bwUtil::execPHP($file);
        } else {
            $folder = $path . $view;
            if (bwFolder::is($folder) && (substr($folder, -1) != '/')) {
                bwUtil::redirect("{$view}/", true, true);
            }
        }

        //
        if ($html === false) {
            $view = "{$template_prefix}/error/404";
            $html = bwUtil::execPHP(BW_PATH_TEMPLATES . DS . $template . '/html/error/404.php');

            bwError::header404();
            bwDebug::addHeader("File not found $file");
        }

        //
        if (isset($routes[$view])) {
            $type = $routes[$view]['type'];

            switch ($type) {
                case 'static':
                    $this->setHtml($html);
                    return;
                    break;
                case 'task':
                    $this->setHtml($html);
                    return;
                    break;
            }
        }

        // type = view
        $this->setHtml(str_replace('{BW VIEW}', $html, $this->getHtml()));

        //
        return;
    }

    function getHtmlHead()
    {
        $this->html = str_replace('{BW HEAD}', bwHtml::head(), $this->html);
    }

}
