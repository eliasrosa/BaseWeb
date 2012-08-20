<?
defined('BW') or die("Acesso negado!");

$task = bwRequest::getVar(task);

if ($task == 'usuarioSalvar')
{
    $r = Usuario::salvar(bwRequest::getVar('dados', array()));
}

if ($task == 'usuarioRemover')
{
    $r = Usuario::remover(bwRequest::getVar('dados', array()));
    $r['redirect'] = bwRouter::_('/usuarios/lista');
}

if ($task == 'grupoSalvar')
{
    $r = UsuarioGrupo::salvar(bwRequest::getVar('dados', array()));
}

if ($task == 'grupoRemover')
{
    $r = UsuarioGrupo::remover(bwRequest::getVar('dados', array()));
    $r['redirect'] = bwRouter::_('/usuarios/grupos/lista');
}

die(json_encode($r));
?>
