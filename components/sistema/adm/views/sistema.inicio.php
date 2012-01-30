<?
defined('BW') or die("Acesso negado!");

$u = bwLogin::getInstance()->getSession();
echo bwAdm::msg("<h1>Olá {$u->nome}</h1><p>Seja bem vindo a administração!</p>");
?>