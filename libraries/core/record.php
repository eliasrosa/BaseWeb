<?php

defined('BW') or die("Acesso negado!");

class bwRecord extends Doctrine_Record
{

    public function postSave(Doctrine_Event $event)
    {
        // executa função herdada
        parent::postSave($event);

        if (isset($event->getInvoker()->id)) {
            // pega os dados
            $record = $this->getTable()->find($event->getInvoker()->id);
            if (isset($record->bwImagem) && count($record->bwImagem)) {
                foreach ($record->bwImagem as $imagem) {
                    echo $imagem->upload();
                }
            }

            // remove as imagens marcadas
            foreach (bwRequest::getVar('remover-bwimagem', array()) as $v) {
                list($com, $old, $name) = explode('-', $v);
                if ($record->getComponentName() == $com) {
                    if (isset($record->bwImagem->$name)) {
                        $record->bwImagem->$name->remove();
                    }
                }
            }
        }
    }

    public function postDelete(Doctrine_Event $event)
    {
        // executa função herdada
        parent::postDelete($event);

        // pega os dados
        $record = $event->getInvoker();

        // remove a imagem caso exista um relacinamento com o registro atual
        if (isset($record->bwImagem) && count($record->bwImagem)) {
            foreach ($record->bwImagem as $imagem) {
                echo $imagem->remove();
            }
        }
    }

    public function preHydrate(Doctrine_Event $event)
    {
        // executa função herdada
        parent::preHydrate($event);
        $dat = $event->data;

        // adiciona as imagens
        if (count($this->_imagens)) {
            $dat['bwImagem'] = new stdClass();

            foreach ($this->_imagens as $name => $params) {
                $dat['bwImagem']->$name = bwImagem::getInstance(
                        $this->getComponentName(), 'imagem-' . $name, $dat['id']
                );
            }
        }

        // adiciona as imagens
        if (count($this->_galerias)) {

            $g = new bwGaleria(
                    $this->getComponentName()
                    , get_class($this)
                    , $dat['id']
            );

            foreach ($this->_galerias as $name => $title) {
                $g->addAlbum($name, $title);
            }

            $dat['bwGaleria'] = $g;
        }

        $event->data = $dat;
    }

    /**
     * Funções para o bwImagem
     * *************************************** */
    private $_imagens = array();

    public function addImagem($name = 'default')
    {
        $this->_imagens[$name] = $name;
    }

    /**
     * Funções para o bwGaleria
     * *************************************** */
    private $_galerias = array();

    public function addGaleria($name = 'default', $titulo = 'Galeria de imagens')
    {
        $this->_galerias[$name] = $titulo;
    }

    /**
     * Funções para o Router
     * *************************************** */
    public function getUrl($nome, $tpl_prefix = true)
    {
        return bwRouter::_($nome, $tpl_prefix, $this->data);
    }

    /**
     * Pega o nome da pasta do componente
     * *************************************** */
    function getComponentName()
    {
        $p = get_class($this);

        $reflector = new ReflectionClass($p);
        $path = dirname($reflector->getFileName());

        $component = str_replace(BW_PATH_COMPONENTS . DS, '', $path);
        $component = str_replace(DS . 'models', '', $component);

        if (bwFolder::is(BW_PATH_COMPONENTS . DS . $component)) {
            return $component;
        } else {
            return NULL;
        }
    }

}
