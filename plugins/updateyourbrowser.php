<?php
defined('BW') or die("Acesso negado!");

class bwPluginUpdateyourbrowser
{
	function beforeDisplay()
	{
   		bwHtml::js(BW_URL_JAVASCRIPTS . '/updateyourbrowser/updateyourbrowser.js');		
	}
}
?>
