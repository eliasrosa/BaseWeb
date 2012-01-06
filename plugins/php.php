<?php

defined('BW') or die("Acesso negado!");

class bwPluginPHP
{

    function beforeDisplay()
    {
        $buffer = bwBuffer::getInstance();
        if (preg_match_all("/{php (.*?).php}/i", $buffer->getHtml(), $matches) && !BW_ADM)
        {
            foreach ($matches[0] as $k => $r)
            {
                // retorna ex: '{php contato/file.php}'
                $c = $matches[$k][$k];
                
                // retorna ex: 'contato/file.php'
                $f = $matches[1][$k];

                // troca as '/' por DS
                $page = str_replace('/', DS, $f);
                
                // pega o caminho do arquivo
                $file = bwPhp::getInstance()->getPathFileMod($page);
                
                // abre o arquivo
                $html = bwUtil::execPHP($file);
            
                // altera o html do buffer
                $buffer->setHtml(str_replace($c, $html, $buffer->getHtml()));
            }
        }
    }
}
?>
