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
        $pass1 = bwRequest::getVar('pass', '', 'post');
        $pass2 = bwRequest::getVar('pass2', '', 'post');

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
}
?>
