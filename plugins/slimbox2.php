<?php

defined('BW') or die("Acesso negado!");

class bwPluginSlimbox2
{
    function beforeDisplay()
    {
        $buffer = bwBuffer::getInstance();
        if (preg_match('#<a.*rel="\blightbox\b".*>#', $buffer->getHtml()))
        {
            bwHtml::js(BW_URL_JAVASCRIPTS . '/slimbox2/slimbox2.js');
            bwHtml::js(BW_URL_JAVASCRIPTS . '/slimbox2/comum.js');
            bwHtml::css(BW_URL_JAVASCRIPTS . '/slimbox2/slimbox2.css');
        }
    }
}
?>
