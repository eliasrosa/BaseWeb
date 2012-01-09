<?php

defined('BW') or die("Acesso negado!");

class bwComponent extends bwObject
{
    var $id = '';
    var $nome = '';
    var $versao = 0;
    var $adm_url_default = 'adm.php?com=xxxx&view=yyyy';
    var $adm_visivel = true;  
    
    //
    public function __construct()
    {
        parent::__construct();

        // carrega as configurações do componente
        $this->loadConfig();
    }

    public function getItemid()
    {
        return bwRequest::getVar('itemid', null);
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


    function getAll()
    {
        $r = array();
        $components = bwFolder::listarConteudo(BW_PATH_COMPONENTS, false, true, false, false);
        sort($components);
        
        foreach($components as $com)
        {
            $file = BW_PATH_COMPONENTS .DS. $com . DS . 'api.php';
            if(bwFile::exists($file))
            {
                $class = 'bw'.ucfirst(strtolower($com));
                $api = call_user_func(array($class, 'getInstance'));

                $r[] = $api->getPublicProperties();
            }
        }
        
        return $r;
    }
    
    function getConfig($var, $getCol = 'value')
    {   
        if(count($this->config))
        {
            return $this->config[$var][$getCol];
        }
    }

    function setConfig($var, $value, $getCol = 'value')
    {   
        if(count($this->config))
        {
            return $this->config[$var][$getCol] = $value;
        }
    }    
    
    function saveConfig()
    {   
        $a = array();
        $a[] = $this->configHead;
        
        if(count($this->config))
        {
            $a = array_merge($a, $this->config);
        }
        
        $r = '';
        foreach($a as $v)
            $r .= bwUtil::array2csv($v);

        bwFile::setConteudo($this->configFile, $r);
        
        return;
    }   
    
    function loadConfig()
    {
        $r = array();
        $this->configFile = BW_PATH_COMPONENTS . DS . $this->id . DS . 'config.conf';
        $this->configHead = array('var', 'value', 'default', 'tipo', 'params', 'titulo', 'protegido', 'oculto', 'desc');
        
        $csv = bwFile::getConteudo($this->configFile); 
        $configs = bwUtil::csv2array($csv);

        if(count($configs) > 1)
        {
            $h = $this->configHead;
            unset($configs[0]);
            
            foreach($configs as $conf)
            {
                $i = array();
                foreach($conf as $k=>$v)
                {
                    $i[$h[$k]] = $v;
                }
                $r[$conf[0]] = $i;
            }
        }
        else
            $r = array();

        $this->config = $r;
    }

}
?>
