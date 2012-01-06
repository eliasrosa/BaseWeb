<?php

defined('BW') or die("Acesso negado!");

class bwLoader
{

    function import($filePath, $lib = 'libraries.', $verificar = false)
    {
        $paths = explode('.', $filePath);

        if ($paths[0] == 'components')
        {
            $lib = '';
            $filePath .= '.api';
        }

        if ($paths[0] == 'plugins')
            $lib = '';


        $libPath = $lib ? $lib . $filePath : $filePath;

        $path = str_replace('.', DS, $libPath);

        if ($verificar)
        {
            if (file_exists(BW_PATH . DS . $path . '.php'))
            {
                require_once(BW_PATH . DS . $path . '.php');
                return true;
            }
            else
                return false;
        }

        require_once(BW_PATH . DS . $path . '.php');
        return;
    }

    public static function autoload($className)
    {
        Doctrine::autoload($className);
        Doctrine::modelsAutoload($className);
        bwLoader::autoloadAPI($className, 'core.');
        bwLoader::autoloadAPI($className, 'components.');
        bwLoader::autoloadAPI($className, 'ambiente.');

        bwLoader::modelsAutoload($className);

        return true;
    }

    public static function modelsAutoload($className)
    {
        if (preg_match('#doctrine#', strtolower($className)))
            return;

        $path = BW_PATH_COMPONENTS;

        $dir_handle = @opendir($path) or die("Unable to open $path");

        while ($file = readdir($dir_handle))
        {
            if ($file != "." || $file != ".." || !is_dir($path . DS . $file))
            {
                $folder = $path . DS . $file;
                $class = $folder . DS . 'models' . DS . $className . '.php';

                if (file_exists($class))
                {
                    require_once($class);
                    return;
                }
            }
        }

        closedir($dir_handle);
    }

    public static function autoloadAPI($className, $tipo)
    {
        if (preg_match('#doctrine#', strtolower($className)))
            return;


        $class = strtolower($tipo . preg_replace('#^bw#', '', $className));

        $exist = bwLoader::import($class, 'libraries.', true);

        if ($exist)
            bwLoader::import($class);
    }

}
?>
