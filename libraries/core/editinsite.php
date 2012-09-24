<?php

defined('BW') or die("Acesso negado!");

abstract class bwEditInSite
{

    function getPath($file = false)
    {
        if (!$file)
            return BW_PATH_TEMPLATES;

        $file = bwEditInSite::getPath() . DS . $file;
        return bwFile::exists($file) ? $file : false;
    }

    function getConteudo($file)
    {
        $file = bwEditInSite::getPath($file);

        if ($file === false)
            return false;

        $conteudo = bwFile::getConteudo($file);

        return $conteudo;
    }

    function setConteudo($file, $conteudo)
    {
        $file = bwEditInSite::getPath($file);

        if ($file === false)
            return false;

        return bwFile::setConteudo($file, $conteudo);
    }

    function salvar($file, $conteudo, $tipo)
    {
        return bwEditInSite::setConteudo($file, $conteudo);
    }

}

?>
