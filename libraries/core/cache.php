<?php

defined('BW') or die("Acesso negado!");

class bwCache
{

    static $name = 'bw.cache';

    function get($var, $default = NULL)
    {
        $var = sha1($var);
        if (isset($_SESSION[bwCache::$name][$var]))
            return base64_decode($_SESSION[bwCache::$name][$var]);

        return $default;
    }

    function set($var, $value)
    {
        $var = sha1($var);
        $_SESSION[bwCache::$name][$var] = base64_encode($value);

        return $value;
    }

    function del($var)
    {
        $var = sha1($var);
        unset($_SESSION[bwCache::$name][$var]);

        return;
    }

    function destroy()
    {
        $_SESSION[bwCache::$name] = array();
        return;
    }

}

?>
