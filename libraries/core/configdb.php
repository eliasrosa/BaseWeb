<?php

defined('BW') or die("Acesso negado!");

class bwConfigDB
{

    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    private $_prefix = '';

    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
    }

    public function getPrefix()
    {
        return $this->_prefix;
    }

    public function setVar($var, $value)
    {
        $db = $this->getVar($var);

        if (!$db->protegido)
            $db->value = $value;

        $db->save();

        return bwComponent::retorno($db);
    }

    public function getVar($var)
    {
        $prefix = $this->getPrefix();
        if ($prefix) {
            $var = str_replace("{$prefix}.", "", $var);
            $var = "{$prefix}.{$var}";
        }

        $dql = Doctrine_Query::Create()
            ->from('Configuracao')
            ->where('var = ?', $var)
            ->fetchOne();

        return $dql;
    }

    public function resetVar($var)
    {
        $dados = $this->getVar($var);
        return $this->setVar($var, $dados->default);
    }

    public function getValue($var)
    {
        return $this->getVar($var)->value;
    }

    public function createHtmlPainel()
    {
        $template = bwTemplate::getInstance();
        $prefix = $this->getPrefix();
        require_once $template->getPathHtml() . DS . 'config' . DS . 'painel.php';
    }

    public function createHtmlForm($var)
    {
        $db = $this->getVar($var);

        if ($db) {

            $this->_form = new bwForm($db, bwRouter::_("/config/save"), 'post', $db->protegido, 'var');
            $this->_form->addInputID();


            //
            $this->_form->addInput('desc', 'text', array('edit' => false, 'value' => nl2br($db->value)));

            //
            $this->_addInputTipo($db);

            //
            //$this->_form->addInput('tipo', 'text', array('edit' => false));
            //
            $this->_addBottonSalvar($db);

            //
            $this->_form->show();
        }
        else
            echo bwAdm::msg("<h1>Variável não encontrada!</h1><p>A variável de configuração '<b>{$var}</b>' não foi encontrada!</p>", true);
    }

    private function _addBottonSalvar($db)
    {
        $this->_form->addBottonSalvar('salvarConfig', 'Salvar configuração');
    }

    private function _addInputTipo($db)
    {
        // atributos
        $attr = array();

        switch ($db->tipo) {
            case 'string':
                $this->_form->addInput('value', 'text', $attr);
                $this->_form->addInput('default', 'text', array('edit' => false));
                break;

            case 'textarea':
                $this->_form->addTextArea('value', $attr);
                $this->_form->addInput('default', 'text', array('edit' => false));
                break;

            case 'bool':
                $this->_form->addBoolean('value', $attr);
                $this->_form->addInput('default', 'text', array('edit' => false, 'value' => 'sdasd' ? 'Sim' : 'Não'));
                break;

            case 'select':

                // Exemplo de json: {"key":["teste1", "teste2", "teste3"], "val":["bola1", "bola2", "bola3"]}
                $params = json_decode($db->params, true);

                if (count($params['key']) != count($params['val']))
                    echo bwAdm::msg('Parámetro inválido!', 1);

                $opcoes = array();
                for ($i = 0; $i < count($params['key']); $i++)
                    $opcoes[$params['key'][$i]] = $params['val'][$i];

                $this->_form->addSelect('value', $opcoes, $attr);
                $this->_form->addInput('default', 'text', array('edit' => false));
                break;

            case 'selectFolder':

                // Exemplo de json: {"path":"%BW_PATH_TEMPLATES%"}
                $params = json_decode($db->params, true);

                // troca '/' -> DS
                $path = str_replace("/", DS, $params['path']);

                // troca %constantes% -> valor real
                $contants = get_defined_constants(true);
                foreach ($contants['user'] as $k => $v)
                    $path = str_replace("%{$k}%", $v, $path);

                // loop nas pastas
                if (!is_dir($path))
                    echo bwAdm::msg("<p>O diretório não existe!</p><p>Pasta: {$path}</p>", 1);
                else {
                    // pega a lista de pasta e arquivos
                    $folders = scandir($path);

                    // remove '.' da array
                    array_shift($folders);

                    // remove '...' da array
                    array_shift($folders);

                    // cria as opcões
                    $opcoes = array();
                    foreach ($folders as $f)
                        if (is_dir($path . DS . $f))
                            $opcoes[$f] = $f;

                    // cria o select
                    $this->_form->addSelect('value', $opcoes, $attr);
                    $this->_form->addInput('default', 'text', array('edit' => false));
                }

                break;

            case 'selectFile':

                // Exemplo de json: {"path":"%BW_PATH_TEMPLATE/css%"}
                $params = json_decode($db->params, true);

                // troca '/' -> DS
                $path = str_replace("/", DS, $params['path']);

                // troca %constantes% -> valor real
                $contants = get_defined_constants(true);
                foreach ($contants['user'] as $k => $v)
                    $path = str_replace("%{$k}%", $v, $path);

                // loop nas pastas
                if (!is_dir($path))
                    echo bwAdm::msg("<p>O diretório não existe!</p><p>Pasta: {$path}</p>", 1);
                else {
                    // pega a lista de pasta e arquivos
                    $files = scandir($path);

                    // remove '.' da array
                    array_shift($files);

                    // remove '...' da array
                    array_shift($files);

                    // cria as opcões
                    $opcoes = array();
                    foreach ($files as $f)
                        if (is_file($path . DS . $f))
                            $opcoes[$f] = $f;

                    // cria o select
                    $this->_form->addSelect('value', $opcoes, $attr);
                    $this->_form->addInput('default', 'text', array('edit' => false));
                }
                break;

            case 'selectQueryDB':

                // Exemplo de json: {"tabela": "MenuItem", "colVal": "titulo"}
                $params = json_decode($db->params, true);

                $tabela = $params['tabela'] ? $params['tabela'] : '';
                $order = $params['order'] ? $params['order'] : '';
                $where = $params['where'] ? $params['where'] : '';
                $colKey = $params['colKey'] ? $params['colKey'] : 'id';
                $colVal = $params['colVal'] ? $params['colVal'] : 'nome';

                $attr = array(
                    'order' => $order,
                    'db.key' => $colKey,
                    'db.value' => $colVal,
                    'where' => $where,
                );

                $this->_form->addSelectDB('value', $tabela, $attr);
                $this->_form->addInput('default', 'text', array('edit' => false));
                break;

            default:
                $this->_form->addTextArea('value', $attr);
                $this->_form->addInput('default', 'text', array('edit' => false));
                break;
        }
    }

}

?>
