<?php

defined('BW') or die("Acesso negado!");

class bwFile
{
    function upload($campo, $dest, $params = array())
    {
        $params = array_merge(array(
                    'ext.permitidas' => '#jpg|gif|png|bmp|swf|flv#',
                    'sobrescrever' => true,
                        ), $params);

        $f = $_FILES[$campo];

        if($f['size'] <= 0)
            return false;

        $ext = bwFile::getExt($f['name']);

        if (isset($_FILES[$campo]))
        {
            $ext = strtolower(substr(basename($f['name']), strrpos(basename($f['name']), '.') + 1));
            if ($f['size'] > 0 && preg_match($params['ext.permitidas'], $ext))
            {
                if (bwFile::exists($dest))
                {
                    if ($params['sobrescrever'])
                        bwFile::remove($dest);
                    else
                        return false;
                }

                if (move_uploaded_file($f['tmp_name'], $dest))
                    return true;
                else
                    return false;
            }
            else
                return false;
        }
        return false;
    }

    function getExt($file)
    {
        return strtolower(substr(basename($file), strrpos(basename($file), '.') + 1));
    }

    function getName($file, $ext = true)
    {
        if ($ext)
            $f = basename($file);
        else
        {
            $e = bwFile::getExt($file);
            $f = basename($file, ".{$e}");
        }

        return $f;
    }

    function remove($file)
    {
        return unlink($file);
    }

    function is($file)
    {
        return is_file($file);
    }

    function exists($file)
    {
        return file_exists($file);
    }

    function size($file)
    {
        return filesize($file);
    }

    function getConteudo($file)
    {
        if(!bwFile::exists($file))
            return false;
            
        $fp = fopen($file, "r");
        $conteudo = @fread($fp, bwFile::size($file));
        fclose($fp);
        
        return $conteudo;
    }

    function setConteudo($file, $conteudo)
    {
        if(!bwFile::exists($file))
            return false;    
    
        $fp = fopen($file, "w");
        $w = @fwrite($fp, $conteudo);
        fclose($fp);
        
        return $w;
    }

}
?>
