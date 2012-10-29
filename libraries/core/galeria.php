<?php

class bwGaleria
{

    private
        $_component,
        $_model,
        $_record_id,
        $_albuns = array();

    public function __construct($component, $model, $record_id)
    {
        $this->_model = $model;
        $this->_component = $component;
        $this->_record_id = $record_id;
    }

    public function addAlbum($album_name, $title)
    {
        $key = $this->getAlbumKey($album_name);
        $this->_albuns[$key] = $title;
    }

    public function getImagensDql($album_name = 'default')
    {
        return Doctrine_Query::create()
                ->from('PlgGaleriaImagem i')
                ->innerJoin('i.Album a')
                ->addWhere('a.key = ?', $this->getAlbumKey($album_name))
                ->addWhere('i.record_id = ?', $this->_record_id)
                ->addWhere('i.status = 1')
                ->orderBy('i.ordem ASC');
    }

    public function getImagens($album_name = 'default')
    {
        $dql = $this->getImagensDql($album_name)
            ->execute();

        return $dql;
    }

    public function getAlbumKey($album_name)
    {
        return sprintf('%s::%s::%s'
                , $this->_component
                , $this->_model
                , $album_name
        );
    }

    public function __get($name)
    {
        $key = $this->getAlbumKey($name);
        if (isset($this->_albuns[$key])) {
            return $this->getImagens($name);
        } else {
            die("[Error] bwGaleria: Álbum não encontrado! ({$key})");
        }
    }

    public function getAdmUrl($album_name = 'default')
    {
        $key = $this->getAlbumKey($album_name);

        if (isset($this->_albuns[$key])) {

            $key = bwUtil::createSafeValue($key . '::' . $this->_record_id);
            return bwRouter::_('/galeria/album?keysafe=' . $key);
        }

        die("[Error] bwGaleria: Álbum não encontrado! ({$key})");
    }

}