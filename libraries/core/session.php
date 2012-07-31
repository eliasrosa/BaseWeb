<?php

defined('BW') or die("Acesso negado!");
@session_start();

class bwSession
{

    function init()
    {
        $limite = 7200;
        $expire = bwSession::get('expire', 0);
        date_default_timezone_set("Brazil/East");

        if ($expire && (time() - $expire) > $limite)
            bwSession::destroy();
        else
            bwSession::set('expire', time());
    }

    function getToken()
    {
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = sha1(rand() . BW_URL_BASE2);
        }
        
        return $_SESSION['token'];
    }

    function get($var, $default = NULL)
    {
        $token = bwSession::getToken();
        $var = 'bw.' . $var;
        if (isset($_SESSION[$token][$var]))
            return $_SESSION[$token][$var];

        return $default;
    }

    function set($var, $value)
    {
        $var = 'bw.' . $var;
        $token = bwSession::getToken();
        $_SESSION[$token][$var] = $value;

        return $value;
    }

    function del($var)
    {
        $var = 'bw.' . $var;
        $token = bwSession::getToken();
        if (isset($_SESSION[$token][$var]))
            unset($_SESSION[$token][$var]);
    }

    function destroy()
    {
        $_SESSION = array();
        session_unset();
        session_destroy();

        @session_start();
        session_regenerate_id();
    }

}

?>
