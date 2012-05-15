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
        $token = bwSession::get('token', false);

        if ($token !== false)
            return $token;
        else {
            $token = sha1(rand() . session_name() . session_id());
            return bwSession::set('token', $token);
        }
    }

    function get($var, $default = NULL)
    {
        $var = 'bw.' . $var;
        if (isset($_SESSION[$var]))
            return $_SESSION[$var];

        return $default;
    }

    function set($var, $value)
    {
        $var = 'bw.' . $var;
        $_SESSION[$var] = $value;

        return $value;
    }

    function del($var)
    {
        $var = 'bw.' . $var;
        if (isset($_SESSION[$var]))
            unset($_SESSION[$var]);
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
