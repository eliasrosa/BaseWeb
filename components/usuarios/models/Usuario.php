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
            'notnull' => false,
            'notblank' => false,
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

    /**
     * Localiza usuário pelo endereço de e-mail,
     * retorna false caso não encontre
     * 
     * @param string $email
     * @return Bool(false) || Doctrine_Record
     */
    public function findByEmail($email)
    {
        if (!bwUtil::isEmail($email))
            return false;

        $dql = Doctrine_Query::create()
            ->from('Usuario u')
            ->where('u.email = ?', $email)
            ->fetchOne();

        return $dql;
    }

    function salvar($dados)
    {
        $r = array();

        // labels
        $u = new Usuario();
        $r['labels'] = $u->labels;

        // POST
        $id = (int) $dados['id'];
        $pass1 = bwRequest::getVar('pass', '', 'post');
        $pass2 = bwRequest::getVar('pass2', '', 'post');

        // onInsert
        if (!$id) {
            // data de registro
            $dados['dataRegistro'] = date('Y-m-d H:i:s');

            // is empty
            if ($pass1 == '') {
                $r['camposErros']['pass'][] = 'Digite uma senha!';
                return bwComponent::retorno(false, $r);
            } else {
                if ($pass1 == $pass2)
                    $dados['pass'] = bwLogin::gerarSenha($dados['user'], $pass1);
                else {
                    $r['camposErros']['pass'][] = 'As senhas deve ser iguais!';
                    return bwComponent::retorno(false, $r);
                }
            }

            // cria um usuario
            if ($dados['user'] == '') {
                $dados['user'] = sha1('bw::' . time());
            }

            // salva no banco
            $db = bwComponent::save('Usuario', $dados);
            $r = bwComponent::retorno($db);

            return $r;
        }

        // onEdit
        else {
            // encontra dados anteriores do banco e mescla com os dados recebidos
            $dados = array_merge(bwComponent::openById('Usuario', $id)->toArray(), $dados);

            // gera a senha
            if ($pass1 != '') {
                if ($pass1 == $pass2)
                    $dados['pass'] = bwLogin::gerarSenha($dados['user'], $pass1);
                else {
                    $r['camposErros']['pass'][] = 'As senhas deve ser iguais!';
                    return bwComponent::retorno(false, $r);
                }
            }

            // salva no banco
            $db = bwComponent::save('Usuario', $dados);
            $r = bwComponent::retorno($db);

            return $r;
        }
    }

    public function remover($dados)
    {
        $db = bwComponent::remover('Usuario', $dados);
        $r = bwComponent::retorno($db);

        return $r;
    }

}