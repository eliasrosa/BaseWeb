<?
defined('BW') or die("Acesso negado!");


$tab = bwRequest::getVar('tab', 'core.site');

echo bwAdm::createHtmlSubMenu($tab);

$config = new bwConfigDB();
$config->setPrefix($tab);
$config->createHtmlPainel();

?>