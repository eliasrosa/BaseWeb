<?php

defined('BW') or die("Acesso negado!");

class bwBrowser
{

    private $_agent = NULL;
    private $_browser = NULL;
    private $_version = NULL;

    function __construct()
    {
        $this->_agent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match('|MSIE ([0-9].[0-9]{1,2})|', $this->_agent, $matched)) {
            $v = $matched[1];
            $b = 'ie';
        } elseif (preg_match('|Opera/([0-9].[0-9]{1,2})|', $this->_agent, $matched)) {
            $v = $matched[1];
            $b = 'opera';
        } elseif (preg_match('|Firefox/([0-9\.]+)|', $this->_agent, $matched)) {
            $v = $matched[1];
            $b = 'firefox';
        } elseif (preg_match('|Chrome/([0-9\.]+)|', $this->_agent, $matched)) {
            $v = $matched[1];
            $b = 'chrome';
        } elseif (preg_match('|Safari/([0-9\.]+)|', $this->_agent, $matched)) {
            $v = $matched[1];
            $b = 'safari';
        } else {
            // browser not recognized!
            $v = 0;
            $b = 'other';
        }

        $this->_version = $v;
        $this->_browser = $b;
    }

    public function getAgent()
    {
        return $this->_agent;
    }

    public function getVersion()
    {
        return $this->_version;
    }

    public function getBrowser()
    {
        return $this->_browser;
    }

    /**
     *
     * @param type $condition 
     */
    public function isBrowser($condition)
    {
        $condition = strtolower($condition);
        list($browser, $operator, $version) = explode(' ', $condition);

        if ($this->_browser == $browser) {

            if (!is_null($operator) && !is_null($version)) {
                return version_compare($this->_version, $version, $operator);
            }
            
            return true;
        }

        return false;
    }

}

?>
