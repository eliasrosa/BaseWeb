<?php
defined('BW') or die( "Acesso Restrito" );

require_once(dirname(__FILE__) .DS. 'helper.php');

$com = bwRequest::getVar('com');

// parametros
$params = array_merge(array(
	'titulo' => 'Menu',
	'tagId' => 'mod-memu',
	'tagClass' => 'mod-menu'
), $params);

echo "\n";
echo '<div id="' .$params['tagId']. '" class="' .$params['tagClass']. '">';
echo '<h2><span>' .$params['titulo']. '</span></h2>';

$menu = new bwModuleAdmMenu();
$menu->show($menu);

echo '</div>';

?>