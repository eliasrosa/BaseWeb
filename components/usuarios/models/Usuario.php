<?php

class Usuario extends bwRecord
{
    var $labels = array(
        'id' => 'ID',
        'idgrupo' => 'Grupo',
        'nome' => 'Nome',
        'email' => 'E-mail',
        'user' => 'Usuário',
        'pass' => 'Senha',
        'status' => 'Status',
        'dataLastVisit' => 'Último acesso',
        'dataRegistro' => 'Data de registro',
        'lastIp' => 'Último IP',
        'lastSessionId' => 'ID da última sessão',
    );
    public function setTableDefinition()
    {
        $this->setTableName('bw_usuarios');
        $this->hasColumn('id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('idgrupo', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('nome', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'unique' => true,
            'notnull' => true,
            'notblank' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('email', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'email' => true,
            'unique' => true,
            'notnull' => true,
            'notblank' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('user', 'string', 50, array(
            'type' => 'string',
            'length' => 50,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'notblank' => true,
            'unique' => true,
            'nospace' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('pass', 'string', 40, array(
            'type' => 'string',
            'length' => 40,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('status', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('dataLastVisit', 'timestamp', null, array(
            'type' => 'timestamp',
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('dataRegistro', 'timestamp', null, array(
            'type' => 'timestamp',
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('lastIp', 'string', 15, array(
            'type' => 'string',
            'length' => 15,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'autoincrement' => false,
        ));
        $this->hasColumn('lastSessionId', 'string', 255, array(
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

        $this->hasOne('UsuarioGrupo as Grupo', array(
            'local' => 'idgrupo',
            'foreign' => 'id'
        ));
    }

}