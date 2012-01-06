<?
defined('BW') or die("Acesso negado!");

if ($task == 'usuariosLista')
{
    $r = bwUsuarios::getInstance()->usuariosLista();
}

if ($task == 'usuarioSalvar')
{
    $dados = bwRequest::getVar('dados', array(), 'post');
    $r = bwUsuarios::getInstance()->usuarioSalvar($dados);
}

if ($task == 'usuarioRemover')
{
    $dados = bwRequest::getVar('dados', array(), 'post');
    $r = bwUsuarios::getInstance()->usuarioRemover($dados);
    $r['redirect'] = bwRouter::_('adm.php?com=usuarios&view=lista');
}

die(json_encode($r));
?>
