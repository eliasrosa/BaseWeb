<?php

class Configuracao extends bwRecord
{
    var $labels = array( 
        'var' => 'Variável',
        'value' => 'Valor',
        'default' => 'Valor padrão',
        'tipo' => 'Tipo de valor',
        'params' => 'Parâmetros',
        'titulo' => 'Título',
        'oculto' => 'Oculto',
        'protegido' => 'Protegido',
        'desc' => 'Descrição'
    );

    public function setTableDefinition()
    {
        $this->setTableName('bw_configuracoes');
        $this->hasColumn('var', 'string', 50, array(
             'type' => 'string',
             'length' => 50,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('value', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('default', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('tipo', 'string', 20, array(
             'type' => 'string',
             'length' => 20,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('protegido', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('oculto', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('titulo', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('desc', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('params', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
    }
}
