<?php
defined('BW') or die("Acesso negado!");

class bwCache
{
    function get($var, $default = NULL)
    {
        $cache = bwSession::get('cache', array());
        $var = sha1($var);
                
        if(isset($cache[$var]))
            return $cache[$var];
            
        return $default;
    }

    function set($var, $value)
    {
        $cache = bwSession::get('cache', array());
        $var = sha1($var);
        $cache[$var] = $value;
        
        bwSession::set('cache', $cache);
        return $value;
    }

    function del($var)
    {
        $cache = bwSession::get('cache', array());
        $var = sha1($var);
        
        unset($cache[$var]);
        
        return bwSession::set('cache', $cache);
    }

    function destroy()
    {
        return bwSession::set('cache', array());
    }
}
?>
