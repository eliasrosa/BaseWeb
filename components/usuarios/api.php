<?php

defined('BW') or die("Acesso negado!");

class bwUsuarios extends bwComponent
{
    // variaveis obrigatórias
    var $id = 'usuarios';
    var $nome = 'Usuários';
    var $adm_url_default = 'adm.php?com=usuarios&view=perfil';
    var $adm_visivel = true;
        

    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
    

    function usuarioSalvar($dados)
    {
        $r = array();

        // labels
        $u = new Usuario();
        $r['labels'] = $u->labels;

        // POST
        $id = (int) $dados['id'];
        $pass1 = $dados['pass'];
        $pass2 = bwRequest::getVar('pass2', '', 'post');

        // remove senha is empty
        if ($pass1 == '')
            unset($dados['pass']);

        // onInsert
        if (!$id)
        {
            // data de registro
            $dados['dataRegistro'] = date('Y-m-d H:i:s');

            // is empty
            if ($pass1 == '')
            {
                $r['camposErros']['pass'][] = 'Digite uma senha!';
                return $this->retorno(false, $r);
            }
            else
            {
                if ($pass1 == $pass2)
                    $dados['pass'] = bwLogin::gerarSenha($dados['user'], $pass1);
                else
                {
                    $r['camposErros']['pass'][] = 'As senhas deve ser iguais!';
                    return $this->retorno(false, $r);
                }
            }

            // salva no banco
            $db = $this->save('Usuario', $dados);
            return $this->retorno($db, $r);
        }

        // onEdit
        else
        {
            // encontra dados anteriores do banco e mescla com os dados recebidos
            $dados = array_merge(bwComponent::openById('Usuario', $id)->toArray(), $dados);

            // gera a senha
            if ($pass1 != '')
            {
                if ($pass1 == $pass2)
                    $dados['pass'] = bwLogin::gerarSenha($dados['user'], $pass1);
                else
                {
                    $r['camposErros']['pass'][] = 'As senhas deve ser iguais!';
                    return $this->retorno(false, $r);
                }
            }

            // salva no banco
            $db = $this->save('Usuario', $dados);
            return $this->retorno($db, $r);
        }
    }

    public function usuarioRemover($dados)
    {
        $id = isset($dados['id']) ? $dados['id'] : $dados;
        $dados = $db = bwComponent::openById('Usuario', $id);

        $dados->delete();
        return $this->retorno($dados);
    }

    public function usuariosLista()
    {
        // Colunas para a ordenação
        $colunas = array('u.id', 'u.nome', 'u.user', 'u.email', 'g.nome', 'u.status');

        // Total de colunas
        $totalColunas = count($colunas);

        // valor de busca
        $search = bwRequest::getVar('sSearch', '');
        $iDisplayLength = bwRequest::getVar('iDisplayLength');
        $iDisplayStart = bwRequest::getVar('iDisplayStart', 0);

        // SQL para a consulta
        $dqlPrincipal = Doctrine_Query::create()
                        ->from('Usuario u')
                        ->where('u.nome LIKE :search OR u.id LIKE :search OR u.email LIKE :search OR u.user LIKE :search', array(':search' => "%{$search}%"));

        // Consulta banco 1
        $dql = $dqlPrincipal
                        ->orderBy($colunas[bwRequest::getVar('iSortCol_0')] . ' ' . bwRequest::getVar('sSortDir_0', 'asc'))
                        ->offset($iDisplayStart)
                        ->limit($iDisplayLength)
                        ->execute();

        // Consulta banco 2
        $totalRegistros = $dqlPrincipal
                        ->select('COUNT(' . $colunas[0] . ') AS total')
                        ->fetchOne();

        // Total de registros
        $iTotalRecords = $totalRegistros->total;

        // Total de registros exibidos
        $iTotalDisplayRecords = $iTotalRecords;

        // Dados das colunas
        $aaData = array();
        foreach ($dql as $i)
        {
            $linkUsuario = '<a href="' . bwRouter::_('adm.php?com=usuarios&view=cadastro&id=' . $i->id) . '">%s</a>';
            $linkGrupo = '<a href="' . bwRouter::_('adm.php?com=usuarios&sub=grupos&view=cadastro&id=' . $i->Grupo->id) . '">%s</a>';

            $aaData[] = array(
                $i->id,
                sprintf($linkUsuario, $i->nome),
                sprintf($linkUsuario, $i->user),
                sprintf($linkUsuario, $i->email ? $i->email : '[Não definido]'),
                sprintf($linkGrupo, $i->Grupo->nome),
                bwAdm::getImgStatus($i->status)
            );
        }

        // retorno ao DataTable
        $retorno = array(
            'sEcho' => bwRequest::getVar('sEcho'),
            'iTotalRecords' => $iTotalRecords,
            'iTotalDisplayRecords' => $iTotalDisplayRecords,
            'aaData' => $aaData
        );

        return $retorno;
    }

}
?>
