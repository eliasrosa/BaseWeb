<?php
defined('BW') or die("Acesso negado!");

if (get_magic_quotes_gpc()) {
    die("Error security risk: Set magic_quotes_gpc Off");
}

// set locate
date_default_timezone_set('America/Sao_Paulo');

// PATH
define('BW_PATH_SCRIPTS', BW_PATH .DS. 'scripts');
define('BW_PATH_CACHE', BW_PATH .DS. 'cache');
define('BW_PATH_MEDIA', BW_PATH .DS. 'media');
define('BW_PATH_PLUGINS', BW_PATH .DS. 'plugins');
define('BW_PATH_LIBRARIES', BW_PATH .DS. 'libraries');
define('BW_PATH_TEMPLATES', BW_PATH .DS. 'templates');
define('BW_PATH_COMPONENTS', BW_PATH .DS. 'components');
define('BW_PATH_DOCTRINE', BW_PATH_LIBRARIES .DS. 'doctrine');
define('BW_PATH_MODELS', BW_PATH_LIBRARIES .DS. 'models');

// URL
define('BW_URL_BASE', bwConfig::$url_base);
define('BW_URL_BASE2', 'http://'. $_SERVER['HTTP_HOST'] . BW_URL_BASE);
define('BW_URL_TEMPLATES', BW_URL_BASE2 . '/templates');
define('BW_URL_MEDIA', BW_URL_BASE2 . '/media');
define('BW_URL_CACHE', BW_URL_BASE2 . '/cache');
define('BW_URL_LIBRARIES', BW_URL_BASE2 . '/libraries');
define('BW_URL_JAVASCRIPTS', BW_URL_LIBRARIES . '/javascripts');
define('BW_URL_COMPONENTS', BW_URL_BASE2 .'/components');
define('BW_URL_PLUGINS', BW_URL_BASE2 .'/plugins');
