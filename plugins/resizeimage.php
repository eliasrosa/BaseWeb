<?php
defined('BW') or die("Acesso negado!");

class bwPluginResizeImage
{
	function beforeDisplay()
	{
		bwLoader::import('plugins.resizeimage.helper');
		bwPluginResizeImageHelper::buscaTags();
	}
}
?>
