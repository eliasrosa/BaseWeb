<?php

class PlgGaleriaAlbum extends Doctrine_Record
{

    public function setTableDefinition()
    {
        $this->setTableName('bw_plg_galerias_albuns');
        $this->hasColumn('id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('key', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'autoincrement' => false,
        ));
    }

    public function setUp()
    {
        parent::setUp();

        $this->hasMany('PlgGaleriaImagens as Imagens', array(
            'local' => 'id',
            'foreign' => 'album_id'
        ));
    }

}
