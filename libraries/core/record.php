<?php

defined('BW') or die("Acesso negado!");

class bwRecord extends Doctrine_Record
{

    public function postSave(Doctrine_Event $event)
    {
        // executa função herdada
        parent::postSave($event);

        if (isset($event->getInvoker()->id)) {
            // prega os dados
            $record = $this->getTable()->find($event->getInvoker()->id);

            // envia a imagem caso exista um relacinamento com o registro atual
            if (isset($record->bwImagem) && is_object($record->bwImagem))
                $record->bwImagem->upload();
        }
    }

    public function postDelete(Doctrine_Event $event)
    {
        // executa função herdada
        parent::postDelete($event);

        // prega os dados
        $record = $event->getInvoker();

        // remove a imagem caso exista um relacinamento com o registro atual
        if (isset($record->bwImagem) && is_object($record->bwImagem))
            $record->bwImagem->remover();
    }

    public function preHydrate(Doctrine_Event $event)
    {
        // executa função herdada
        parent::preHydrate($event);

        $dat = $event->data;

        if ($this->_bwImagem && isset($dat['id']))
            $dat['bwImagem'] = bwImagem::getInstance($this->_bwImagemCom, $this->_bwImagemSub, $dat['id']);
        
        $event->data = $dat;
    }

    /*
     * Funções para o bwImagem
     *
     */

    private $_bwImagem = false;
    private $_bwImagemCom = null;
    private $_bwImagemSub = null;

    public function setBwImagem($com, $sub)
    {
        $this->_bwImagem = true;
        $this->_bwImagemCom = $com;
        $this->_bwImagemSub = $sub;
    }


    public function getUrl($nome){
        $i = $this->data;
        return bwRouter::getUrl($nome, $i);
    }
}

?>
