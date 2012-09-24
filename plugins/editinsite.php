<?php

defined('BW') or die("Acesso negado!");

class bwPluginEditinsite
{

    function beforeDisplay()
    {
        $buffer = bwBuffer::getInstance();

        if (preg_match_all('#{EDITINSITE (.*)}#', $buffer->getHtml(), $matches)) {

            $login = bwLogin::getInstance();

            foreach ($matches[1] as $file) {

                $file_path = bwTemplate::getInstance()->getName() . DS . 'edit-in-site' . DS . $file;
                $conteudo = bwEditInSite::getConteudo($file_path);
                $tipo = bwFile::getExt($file);

                if (bwEditInSite::getPath($file_path) !== false) {

                    // verifica se esta logado                    
                    if ($login->isLogin() && $login->getSession()->Grupo->isAdm) {

                        bwHtml::css(BW_URL_JAVASCRIPTS . '/prettyPhoto/css/prettyPhoto.css');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/prettyPhoto/jquery.prettyPhoto.js');

                        bwHtml::css(BW_URL_JAVASCRIPTS . '/wysiwyg/jquery.wysiwyg.css');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/wysiwyg/jquery.wysiwyg.js');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/wysiwyg/controls/wysiwyg.image.js');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/wysiwyg/controls/wysiwyg.link.js');
                        bwHtml::js(BW_URL_JAVASCRIPTS . '/wysiwyg/controls/wysiwyg.table.js');

                        bwHtml::css(BW_URL_PLUGINS . '/editinsite/editinsite.css');
                        bwHtml::js(BW_URL_PLUGINS . '/editinsite/editinsite.js');

                        if (preg_match('#txt|html#', $tipo)) {
                            $url = bwRouter::_(sprintf('/plugins/editinsite/edit.php?key=%s&tipo=%s', bwUtil::createSafeValue($file_path), $tipo));
                            $conteudo .= sprintf('<a class="editinsite" href="%s&ajax=true" data-ext="%s"></a>', $url, $tipo);
                        }
                    }
                }

                // altera o html do buffer
                $buffer->setHtml(str_replace("{EDITINSITE $file}", $conteudo, $buffer->getHtml()));
            }
        }
    }

}

?>
