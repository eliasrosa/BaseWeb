<?php

class PlgGaleriaImagem extends bwRecord
{
    var $labels = array(
       'album_id' => 'album_id',
       'record_id' => 'record_id',
       'title' => 'title',
       'alt' => 'alt',
       'ordem' => 'ordem',
       'position' => 'position',
    );
    
    
    public function setTableDefinition()
    {
        $this->setTableName('bw_plg_galerias_imagens');
        $this->hasColumn('id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('album_id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'notblank' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('record_id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'notblank' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('title', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('alt', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('position', 'string', 100, array(
            'type' => 'string',
            'length' => 100,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('ordem', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'notblank' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('status', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
            'notblank' => false,
            'autoincrement' => false,
        ));
    }

    public function setUp()
    {
        parent::setUp();

        $this->hasOne('PlgGaleriaAlbum as Album', array(
            'local' => 'album_id',
            'foreign' => 'id'
        ));
    }

    public function preHydrate(Doctrine_Event $event)
    {
        // executa função herdada
        $dat = $event->data;
        $album = Doctrine::getTable('PlgGaleriaAlbum')
            ->find($dat['album_id']);

        list($component, $model, $album_name) = explode('::', $album->key);
        $dat['bwImagem'] = bwImagem::getInstance(
                $component, 'galeria-'.$album_name, $dat['id']
        );

        $event->data = $dat;
    }

    public function postSave(Doctrine_Event $event)
    {
        
    }

    public function postDelete(Doctrine_Event $event)
    {
        
    }

    public function preDelete(Doctrine_Event $event)
    {
        
    }

}

