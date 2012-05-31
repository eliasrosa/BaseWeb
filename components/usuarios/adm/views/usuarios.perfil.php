<?
defined('BW') or die("Acesso negado!");

echo bwAdm::createHtmlSubMenu(0);

bwUtil::redirect('adm.php?com=usuarios&view=lista');
