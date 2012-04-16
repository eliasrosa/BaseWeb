<?php

defined('BW') or die("Acesso negado!");

class bwPluginEditinsite
{
    function beforeDisplay()
    {
        $buffer = bwBuffer::getInstance();
        if (preg_match_all('#.*{EDITINSITE (.*)}.*#', $buffer->getHtml(), $matches))
        {
            //print_r($matches);
            $login = bwLogin::getInstance();
            
            foreach ($matches[1] as $file)
            {
                // busca
                $fileFull = bwTemplate::getInstance()->getNome() . DS . 'edit-in-site' . DS . $file;
                $conteudo = bwEditInSite::getConteudo($fileFull);
                $tipo = bwFile::getExt($file);
                
                
                if(bwEditInSite::getPath($fileFull) !== false)
                {
                    // verifica se esta logado                    
                    if($login->isLogin() && $login->getSession()->Grupo->isAdm)
                    {
                        bwHtml::css(BW_URL_JAVASCRIPTS . '/jquery/themes-1.8rc2/redmond/style.css');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/jquery/ui-1.8rc2.js');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/scrollTo-1.4.2-min.js');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/ajaxbox-1.0.3.js');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/outerhtml.js');
                       
                        bwHtml::js('/tiny_mce/jquery.tinymce.js', true);
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/validaform/jquery.meio.mask.min.js');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/validaform/jquery.validaform.js');
                        bwHtml::css(BW_URL_JAVASCRIPTS . '/validaform/jquery.datepicker.css');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/validaform/jquery-ui-timepicker-addon.js');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/validaform/ui.datepicker-pt-BR.js');                       
                       
                        bwHtml::css(BW_URL_JAVASCRIPTS . '/editinsite/editinsite.css');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/editinsite/editinsite.js');
                        
                        if(preg_match('#txt|html#', $tipo))
                        {
                            $url = bwRouter::_('adm.php?com=sistema&view=ajaxbox&sub=editinsite');
                            $conteudo .= sprintf('<span class="editinsite" editinsite-url="%s" editinsite-file="%s" editinsite-tipo="%s"></span>',  $url, $fileFull, $tipo);
                        }
                    }

                    // altera o html do buffer
                    $buffer->setHtml(str_replace("{EDITINSITE $file}", $conteudo, $buffer->getHtml()));
                }
            }
        }
    }
}
?>
