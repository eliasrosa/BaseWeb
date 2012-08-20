<?php

class UsuarioGrupo extends bwRecord
{

    var $labels = array(
        'id' => 'ID',
        'idgrupo' => 'Grupo',
        'nome' => 'Nome',
        'isAdm' => 'Acesso completo',
        'descricao' => 'Descrição do grupo',
        'status' => 'Status',
    );

    public function setTableDefinition()
    {
        $this->setTableName('bw_usuarios_grupos');
        $this->hasColumn('id', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('nome', 'string', 255, array(
            'type' => 'string',
            'length' => 255,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'notblank' => true,
            'unique' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('isAdm', 'integer', 4, array(
            'type' => 'integer',
            'length' => 4,
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('descricao', 'string', null, array(
            'type' => 'string',
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('permissoes', 'string', null, array(
            'type' => 'string',
            'fixed' => false,
            'unsigned' => false,
            'primary' => false,
            'notnull' => false,
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
    }

    public function setUp()
    {
        parent::setUp();

        $this->hasMany('Usuario as Usuarios', array(
            'local' => 'id',
            'foreign' => 'idgrupo'
        ));
    }

    public function salvar($dados)
    {
        $db = bwComponent::save('UsuarioGrupo', $dados);
        $r = bwComponent::retorno($db);

        return $r;
    }

    public function remover($dados)
    {
        // verifica se exite usuários relacionadas
        $dql = Doctrine_Query::create()
            ->from('Usuario')
            ->where('idgrupo = ?', $dados['id']);

        if ($dql->fetchOne()) {
            return array(
                'retorno' => false,
                'msg' => 'Existem usuários relacionados com este grupo!',
            );
        }

        $db = bwComponent::remover('UsuarioGrupo', $dados);
        $r = bwComponent::retorno($db);

        return $r;
    }

}