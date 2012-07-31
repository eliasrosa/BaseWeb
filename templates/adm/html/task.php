<?
defined('BW') or die("Acesso negado!");
$dados = bwRequest::getVar('dados', array(), 'post');

if ($task == 'salvarConfig')
{
    $config = new bwConfigDB();
    $r = $config->setVar($dados['var'], $dados['value']);
}

if ($task == 'salvarInSite')
{
    $r = bwEditInSite::salvar(
        bwRequest::getVar('file'),
        bwRequest::getVar('conteudo'),
        bwRequest::getVar('tipo')
    );
}


die(json_encode($r));

?>