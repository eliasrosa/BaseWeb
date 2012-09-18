<?
defined('BW') or die("Acesso negado!");

$smarty = bwSmarty::getInstance();
$smarty->assign('url_adm', bwRouter::_('/adm'));

