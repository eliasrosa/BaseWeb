<?php

defined('BW') or die("Acesso negado!");

class bwPluginValidaForm
{
    function beforeDisplay()
    {
        $buffer = bwBuffer::getInstance();
        if (preg_match('#<form.*class="?.*validaform?.*".*>#i', $buffer->getHtml()))
        {
            bwHtml::js(BW_URL_JAVASCRIPTS . '/validaform/jquery.meio.mask.min.js');
            bwHtml::js(BW_URL_JAVASCRIPTS . '/validaform/jquery.validaform.js');
            bwHtml::css(BW_URL_JAVASCRIPTS . '/validaform/jquery.datepicker.css');

            if (BW_ADM)
            {
                bwHtml::js(BW_URL_JAVASCRIPTS . '/validaform/jquery-ui-timepicker-addon.js');
                bwHtml::js(BW_URL_JAVASCRIPTS . '/nicEdit/nicEdit.js');
                bwHtml::js(BW_URL_JAVASCRIPTS . '/validaform/ui.datepicker-pt-BR.js');
            }
        }
    }
}
?>
