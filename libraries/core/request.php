<?php

defined('BW') or die("Acesso negado!");

class bwRequest
{
    static $request = array();

    function getAll()
    {
        return bwRequest::$request;
    }

    function getMethod()
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        return $method;
    }

    function setVar($n, $v)
    {
        bwRequest::$request[$n] = $v;

        return $v;
    }

    function setVarTemp($n, $v)
    {
        bwSession::set('tmp.'.$n, $v);
        return $v;
    }

    function getVarTemp($n, $v = NULL)
    {
        $value = bwSession::get('tmp.'.$n, $v);
        bwSession::del('tmp.'.$n);

        return $value;
    }

    function getVar($var, $default = NULL, $method = '', $type = 'string')
    {
        if (isset(bwRequest::$request[$var]))
            return bwRequest::$request[$var];

        $method = ($method == '') ? bwRequest::getMethod() : strtoupper($method);

        if (isset($_GET[$var]) && $method == 'GET')
            return bwRequest::strip_magic_quotes($_GET[$var]);

        elseif (isset($_POST[$var]) && $method == 'POST')
            return bwRequest::strip_magic_quotes($_POST[$var]);

        else
            return $default;
    }

    function strip_magic_quotes($str)
    {
        return get_magic_quotes_gpc() ? stripslashes($str) : $str;
    }

    function getInt($var, $default = 0, $method = '')
    {
        $int = bwUtil::int(bwRequest::getVar($var, $default, $method), $default);
        return $int;
    }

    function checkToken()
    {
        $token = bwRequest::getToken();
        return (bool) bwRequest::getVar($token, false);
    }

    function getToken()
    {
        return bwSession::getToken();
    }
}
?>
