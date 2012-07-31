<?
defined('BW') or die("Acesso negado!");
$dados = bwRequest::getVar('dados', array(), 'post');

$config = new bwConfigDB();
$r = $config->setVar($dados['var'], $dados['value']);

die(json_encode($r));
