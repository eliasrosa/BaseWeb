<?php

defined('BW') or die("Acesso negado!");

class bwComponentOld extends bwObject
{
    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
 
    var $query;
    var $pager = false;
    var $pagerNome = '';
    var $pagerRanger = false;
    var $pagerLayout = false;
    var $pagerOrders = array();
    var $urlTemplate = '';
    var $pagerMaxPerPage = 10;
    var $pagerGetPage = 1;
    var $pagerGetPageVar = 'pag';
    var $pagerLayoutTemplate = '<a class="pag" href="{%url}">{%page}</a>';
    var $pagerLayoutTemplateSelect = '<a href="{%url}" class="pag select active atual">{%page}</a>';
    var $pagerRangerOptions = array('chunk' => 7);
    public function __construct()
    {
        // iniciado os dqls
        $this->dqls = new stdClass();

        // defini max num regitro por página
        $this->pagerMaxPerPage = 12;
    }

    public function setConfig($config)
    {
        if (is_array($config))
            foreach ($config as $var => $value)
                $this->$var = $value;

        return $this;
    }

    public function createPager($dql = null, $config= array())
    {
        // configura
        $this->setConfig($config);

        // verifica a query
        $this->query = is_null($dql) ? $this->query : $dql;
        if (is_string($this->query))
        {
            if (isset($this->dqls->$dql))
                $this->query = $this->dqls->$dql;
            else
                bwError::show("DQL não existe: $dql", 'Erro no ' . __CLASS__);
        }
        $this->pagerGetPageVar = $this->pagerNome . $this->pagerGetPageVar;
        $this->pagerGetPage = bwRequest::getInt($this->pagerGetPageVar, 1);


        $this->_configPagerOrder();
        $this->_configUrl();

        $this->pager = new Doctrine_Pager($this->query, $this->pagerGetPage, $this->pagerMaxPerPage);
        $this->pagerRanger = new Doctrine_Pager_Range_Sliding($this->pagerRangerOptions);

        $this->pagerLayout = new Doctrine_Pager_Layout($this->pager, $this->pagerRanger, $this->urlTemplate);
        $this->pagerLayout->setTemplate($this->pagerLayoutTemplate);
        $this->pagerLayout->setSelectedTemplate($this->pagerLayoutTemplateSelect);

        return $this;
    }

    private function _configPagerOrder()
    {
        $orders = $this->pagerOrders;

        if (count($orders))
        {
            $this->pagerGetOrderVar = $this->pagerNome . 'ordem';
            $this->pagerGetOrder = bwRequest::getInt($this->pagerGetOrderVar, 0);

            if (isset($orders[$this->pagerGetOrder]['sql']))
            {
                $order = $orders[$this->pagerGetOrder]['sql'];
                $this->query = $this->query->orderBy($order);
            }
        }
    }

    public function getPagerOrder()
    {

        $u = bwUrl::getInstance();
        $orders = $this->pagerOrders;

        if (count($orders))
        {
            $this->pagerGetOrderVar = $this->pagerNome . 'ordem';
            $this->pagerGetOrder = bwRequest::getInt($this->pagerGetOrderVar, 0);

            $html = '';
            $options = '';
            foreach ($orders as $k => $ordem)
            {
                $u->setVar($this->pagerGetPageVar, 1);
                $u->setVar($this->pagerGetOrderVar, $k);
                $selected = ($k == $this->pagerGetOrder) ? ' selected="selected"' : '';
                $options .= sprintf('<option value="%s" %s>%s</option> ', bwRouter::_($u->toString()), $selected, $ordem['label']);
            }

            $js = sprintf('<script type="text/javascript">$(function(){ $(\'select#%s\').change(function(){window.location = $(this).val();});});</script>', $this->pagerGetOrderVar);
            $html = sprintf('<select name="%s" id="%s">%s</select>%s', $this->pagerGetOrderVar, $this->pagerGetOrderVar, $options, $js);

            return $html;
        }
    }

    private function _configUrl()
    {

        $u = bwUrl::getInstance();
        $queryString = array_merge($u->getQuery(1), array($this->pagerGetPageVar => '{%page_number}'));
        $u->setQuery($queryString);
        $this->urlTemplate = urldecode($u->toString());
        $u->setVar($this->pagerGetPageVar, $this->pagerGetPage);
    }

    private function getTagID($com = '', $view = '')
    {
        $id = str_replace($com . '.', '', $view);
        $id = str_replace('.', '-', $com . '-' . $id);

        return $id;
    }

    function loadCss($com, $view)
    {
        $css = BW_PATH_COMPONENTS . DS . $com . DS . 'css' . DS . $view . '.css';
        if (bwFile::exists($css))
            bwHtml::css(BW_URL_COMPONENTS . '/' . $com . '/css/' . $view . '.css');
    }

    function load($com = false, $view = false, $params = '')
    {
        $html = '';

        $cFile = bwComponent::check($com);
        $vFile = bwComponent::checkView($com, $view);

        if ($cFile && $vFile)
        {
            if (!BW_ADM)
            {
                $conf = bwObject::getInstance('bw' . $com)->getConfig();
                if ($conf->getValue('style'))
                {
                    $css = BW_PATH_COMPONENTS . DS . $com . DS . 'styles' . DS . $conf->getValue('style') . DS . 'style.css';
                    if (bwFile::exists($css))
                        bwHtml::css(BW_URL_COMPONENTS . '/' . $com . '/styles/' . $conf->getValue('style') . '/style.css');
                }
            }

            ob_start();

            echo '<div id="' . bwComponent::getTagID($com, $view) . '" class="component '.$com.'">';

            bwAdm::loadHead('2');

            require_once($cFile);
            require_once($vFile);

            echo '</div>';

            $html = ob_get_clean();
        }

        return $html;
    }

    function check($com = '__bwRequest__')
    {
        $com = ($com == '__bwRequest__') ? bwRequest::getVar('com', '') : $com;
        $file = BW_PATH_COMPONENTS . DS . $com . DS . 'index.php';

        if (bwFile::exists($file))
            return $file;

        else
            bwError::show("Componente '{$com}' não foi encontrado!");
    }

    function checkView($com = '__bwRequest__', $view = '__bwRequest__')
    {

        $com = ($com == '__bwRequest__') ? bwRequest::getVar('com', '') : $com;
        $view = ($view == '__bwRequest__') ? bwRequest::getVar('view', '') : $view;
        $file = BW_PATH_COMPONENTS . DS . $com . DS . 'views' . DS . $view . '.php';

        if (BW_ADM)
            $file = BW_PATH_COMPONENTS . DS . $com . DS . 'adm' . DS . 'views' . DS . $view . '.php';

        if (bwRequest::getVar('task'))
            return $file;

        $custom = bwTemplate::getInstance()->getPathHtml() . DS . 'com_' . $com . DS . $view . '.php';
        if (bwFile::exists($custom) && !BW_ADM)
            return $custom;

        if (bwFile::exists($file))
            return $file;

        else
            bwError::show("View '{$file}' não foi encontrado!");
    }

    function openById($table, $id)
    {
        $tb = Doctrine::getTable($table);

        $db = $tb->find($id);

        if (!$db)
            $db = $tb->create();

        return $db;
    }

    function retorno($db, $retorno = array())
    {
        $retorno = array_merge(array(
                    'retorno' => true,
                    'redirect' => false,
                    'labels' => array(),
                    'camposErros' => array(),
                        ), $retorno);


        if (is_bool($db) && !$db && count($retorno['camposErros']))
        {
            $dados = array();
            $erros = array();
            $isErros = (!$retorno['retorno'] || count($retorno['camposErros'])) ? true : false;
        }
        else
        {
            $dados = count($db->toArray()) ? $db->toArray() : array();
            $erros = $db->getErrorStack();
            $retorno['labels'] = $db->labels;
            $isErros = (!$retorno['retorno'] || $erros->count() || count($retorno['camposErros'])) ? true : false;
        }

        $camposErros = array();
        $errosMsg = array(
            'unique' => 'O valor informado já existe, este valor deve ser único!',
            'notnull' => 'Campo não encontrado!',
            'notblank' => 'Este campo não pode estar em branco!',
            'email' => 'O e-mail informado não é válido ou não existe!',
            'type' => 'O tipo do valor está incorreto!',
            'integer' => 'O valor informado deve ser um número inteiro!',
            'upload' => 'Houve um erro ao tenta enviar o arquivo!',
            'nospace' => 'O valor não de conter espaços em branco!',
            'alias' => 'O valor informado não deve conter caracteres especiais!',
        );

        if ($isErros)
        {
            $msg = "Os seguintes campos devem ser preenchidos corretamente:\n";
            foreach ($erros as $campo => $errorCodes)
            {
                foreach ($errorCodes as $code)
                {
                    $codeMsg = isset($errosMsg[$code]) ? $errosMsg[$code] : $code;
                    $camposErros[$campo][] = $codeMsg;
                }
            }

            foreach ($retorno['camposErros'] as $campo => $errorCodes)
            {
                foreach ($errorCodes as $code)
                {
                    $codeMsg = isset($errosMsg[$code]) ? $errosMsg[$code] : $code;
                    $camposErros[$campo][] = $codeMsg;
                }
            }

            //  print_r($retorno);

            foreach ($camposErros as $k => $v)
            {
                $msg .= "\n- " . $retorno['labels'][$k];
            }
        }
        else
        {
            $msg = 'Solicitação concluída com sucesso!';
        }

        $retorno = array(
            'retorno' => !$isErros,
            'dados' => bwUtil::array2query($dados),
            'msg' => $msg,
            'camposErros' => $camposErros,
            'redirect' => $retorno['redirect'],
        );

        return $retorno;
    }

    function save($table, $dados = array(), $primary = 'id', $rel = array())
    {
        $tb = Doctrine::getTable($table);

        if (isset($dados[$primary]) && $dados[$primary])
        {
            $pc = $tb->find($dados[$primary]);
            $edit = $dados[$primary];
        }
        else
        {
            $pc = $tb->create();
            $edit = false;
        }

        try
        {
            unset($dados[$primary]);
            $pc->fromArray($dados);

            // relacionamentos
            foreach ($rel as $alias => $ids)
                $pc->unlink($alias)->link($alias, $ids);

            $pc->save();

            $pc = $edit ? $pc : $tb->find($pc->$primary);
        } catch (Doctrine_Validator_Exception $e)
        {
            
        }

        return $pc;
    }

    function remover($table, $dados = array(), $primary = 'id', $rel = array())
    {
        $tb = Doctrine::getTable($table);
        $db = $tb->find($dados[$primary]);

        // relacionamentos
        foreach ($rel as $alias)
            $db->unlink($alias, array(), true);

        $db->delete();
        
        return $db;
    }

}
?>
