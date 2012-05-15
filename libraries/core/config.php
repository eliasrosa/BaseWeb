<?php

defined('BW') or die("Acesso negado!");

class bwConfigCreatorClass
{

    function __construct($className, $arquivo, $vars = array())
    {
        $this->className = $className;
        $this->vars = $vars;
        $this->arquivo = $arquivo;
    }

    function set($var, $value)
    {
        $this->vars[$var] = $value;
    }

    function get($var)
    {
        return $this->vars[$var];
    }

    function create()
    {
        $t = "<?php\n// Arquivo gerado pelo bwConfigCreatorClass()\ndefined('BW') or die('Acesso negado!');\n\nclass {$this->className}\n{\n";

        ksort($this->vars);
        reset($this->vars);

        foreach ($this->vars as $k => $v) {
            $t .= "\tstatic \${$k} = '{$v}';\n";
        }

        $t .= "}\n?>";

        // grava o arquivo
        $fh = fopen($this->arquivo, 'w') or die("Arquivo não localizado!");
        fwrite($fh, $t);
        fclose($fh);
    }

}

?>
