<?php

defined('BW') or die("Acesso negado!");

class bwFolder
{

    function remove($dir)
    {
        if (is_dir($dir)) {
            $files = scandir($dir);
            array_shift($files);    // remove '.' from array
            array_shift($files);    // remove '..' from array

            foreach ($files as $file) {
                $file = $dir . '/' . $file;
                if (is_dir($file)) {
                    bwFolder::remove($file);
                    rmdir($file);
                }
                else
                    unlink($file);
            }

            rmdir($dir);

            return true;
        }

        return false;
    }

    function create($dir, $mode = 0777, $recursive = false)
    {
        return mkdir($dir, $mode, $recursive);
    }

    function is($dir)
    {
        return is_dir($dir);
    }

    function listarConteudo($dir, $listFiles = true, $listFolders = true,
        $listDots = false, $fullPath = false)
    {
        // Abre um diretorio conhecido, e faz a leitura de seu conteudo
        if (bwFolder::is($dir)) {
            if ($dh = opendir($dir)) {

                $r = array();
                while (($file = readdir($dh)) !== false) {
                    $list = false;
                    $path = $dir . DIRECTORY_SEPARATOR . $file;
                    $isDot = ($file == '.' || $file == '..') ? true : false;

                    if ($listFiles && bwFile::is($path))
                        $list = true;

                    if ($listFolders && bwFolder::is($path) && !$isDot)
                        $list = true;

                    if ($listDots && $isDot)
                        $list = true;

                    if ($list && !$fullPath)
                        $r[] = $file;
                    elseif ($list && $fullPath)
                        $r[] = $path;
                }
                closedir($dh);
                return $r;
            }
            else
                return false;
        }
        else
            return false;
    }

}

?>
