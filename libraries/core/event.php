<?php

defined('BW') or die("Acesso negado!");

class bwEvent extends bwObject
{

    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    function display()
    {
        // antes de exibir o html
        bwPlugin::triggerEventAll('beforeDisplay');

        // inicia a instancia  do buffer
        $buffer = bwBuffer::getInstance();

        // adiciona o HEAD e remove {BW HEAD}
        $buffer->getHtmlHead();

        // mostra o template
        print $buffer->getHtml();

        // depois de exibir o html
        bwPlugin::triggerEventAll('afterDisplay');
    }

}

?>
