<?php

defined('BW') or die("Acesso negado!");

class bwUrl
{

    var
        $url,
        $dados;

    function __construct($url = false)
    {
        if ($url)
            $this->url = $url;
        else
            $this->current();

        $this->parseUrl();
    }

    public function getInstance($url = false)
    {
        $class = __CLASS__;
        return new $class($url);
    }

    function current()
    {
        $pageURL = 'http';
        $pageURL .= '://';

        if ($_SERVER['SERVER_PORT'] != '80')
            $pageURL .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        else
            $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        $this->url = $pageURL;

        return;
    }

    function parseUrl()
    {
        $this->dados = parse_url($this->url);

        $this->dados = array_merge(array(
            'scheme' => '',
            'host' => '',
            'port' => '',
            'user' => '',
            'pass' => '',
            'path' => '',
            'query' => '',
            'fragment' => ''
            ), $this->dados);

        return;
    }

    function toString($decodeurl = true)
    {
        $s = "{$this->getScheme()}://";

        $user = $this->getUser();
        $s .= ($user == "") ? "" : "{$user}:";

        $pass = $this->getPass();
        $s .= ($pass == "") ? "" : "{$pass}@";

        $s .= $this->getHost();

        $port = $this->getPort();
        $s .= $port == "" ? "" : ":{$port}";

        $s .= $this->getPath();

        $query = $this->getQuery();
        $s .= $query == "" ? "" : "?{$query}";

        $fragment = $this->getFragment();
        $s .= $fragment == "" ? "" : "#{$fragment}";

        if ($decodeurl)
            return urldecode($s);
        else
            return $s;
    }

    // base64
    function toBase64($decodeurl = true)
    {
        return base64_encode($this->toString($decodeurl));
    }

    // fragment
    function getFragment()
    {
        return $this->dados['fragment'];
    }

    function setFragment($value)
    {
        $this->dados['fragment'] = $value;

        return;
    }

    // host
    function getHost()
    {
        return $this->dados['host'];
    }

    function setHost($value)
    {
        $this->dados['host'] = $value;

        return;
    }

    // pass
    function getPass()
    {
        return $this->dados['pass'];
    }

    function setPass($value)
    {
        $this->dados['pass'] = $value;

        return;
    }

    // path
    function getPath()
    {
        return $this->dados['path'];
    }

    function setPath($value)
    {
        $this->dados['path'] = $value;

        return;
    }

    // port
    function getPort()
    {
        return $this->dados['port'];
    }

    function setPort($value)
    {
        $this->dados['port'] = $value;

        return;
    }

    // scheme
    function getScheme()
    {
        return $this->dados['scheme'];
    }

    function setScheme($value)
    {
        $this->dados['scheme'] = $value;

        return;
    }

    // scheme
    function getUser()
    {
        return $this->dados['user'];
    }

    function setUser($value)
    {
        $this->dados['user'] = $value;

        return;
    }

    // query
    function getQuery($toArray = false)
    {
        if ($toArray)
            return $this->parseQuery($this->dados['query']);
        else
            return $this->dados['query'];
    }

    function setQuery($query)
    {
        if (is_string($query))
            $query = $this->parseQuery($query);

        $query = array_merge($this->getQuery(true), $query);
        $query = $this->buildQuery($query);

        $this->dados['query'] = $query;

        return;
    }

    function parseQuery($query)
    {
        parse_str($query, $out);
        return $out;
    }

    function buildQuery($query)
    {
        if (is_array($query))
            return http_build_query($query, '', '&');

        return null;
    }

    function clearQuery($toArray = false)
    {
        $query = $this->getQuery($toArray);
        $this->dados['query'] = '';

        return $query;
    }

    // var
    function getVar($var, $default = null)
    {
        $query = $this->getQuery(true);

        if (isset($query[$var]))
            return $query[$var];
        else
            return $default;
    }

    function setVar($var, $value)
    {
        $query = "{$var}={$value}";

        $this->setQuery($query);

        return;
    }

    function delVar($var)
    {
        $query = $this->clearQuery(true);

        unset($query[$var]);

        $this->setQuery($query);

        return;
    }

}

?>
