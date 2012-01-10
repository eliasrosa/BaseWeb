<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1); 

defined('BW') or die("Acesso negado!");


// PATH
define('BW_PATH_CACHE', BW_PATH .DS. 'cache');
define('BW_PATH_MEDIA', BW_PATH .DS. 'media');
define('BW_PATH_MODULOS', BW_PATH .DS. 'modules');
define('BW_PATH_PLUGINS', BW_PATH .DS. 'plugins');
define('BW_PATH_LIBRARIES', BW_PATH .DS. 'libraries');
define('BW_PATH_TEMPLATES', BW_PATH .DS. 'templates');
define('BW_PATH_COMPONENTS', BW_PATH .DS. 'components');
define('BW_PATH_DOCTRINE', BW_PATH_LIBRARIES .DS. 'doctrine');
define('BW_PATH_MODELS', BW_PATH_LIBRARIES .DS. 'models');
define('BW_PATH_ADM', BW_PATH . DS . 'adm');
define('BW_PATH_ADM_LOGIN', BW_PATH_ADM . DS . 'login');


// URL
define('BW_URL_BASE', bwConfig::$url_base);
define('BW_URL_BASE2', 'http://'. $_SERVER['HTTP_HOST'] . BW_URL_BASE);
define('BW_URL_TEMPLATES', BW_URL_BASE . '/templates');
define('BW_URL_MEDIA', BW_URL_BASE . '/media');
define('BW_URL_CACHE', BW_URL_BASE . '/cache');
define('BW_URL_LIBRARIES', BW_URL_BASE . '/libraries');
define('BW_URL_JAVASCRIPTS', BW_URL_LIBRARIES . '/javascripts');
define('BW_URL_COMPONENTS', BW_URL_BASE .'/components');
define('BW_URL_MODULOS', BW_URL_BASE .'/modules');
define('BW_URL_ADM', BW_URL_BASE2 . '/adm');
define('BW_URL_ADM_LOGIN', BW_URL_ADM .'/login');
define('BW_URL_ADM_LOGIN_FILE', BW_URL_ADM_LOGIN .'/index.php');
define('BW_URL_ADM_LOGOFF_FILE', BW_URL_ADM_LOGIN .'/sair.php');
define('BW_URL_INSTALL', BW_URL_BASE . '/install.php');

// logo
define('BW_PATH_LOGO', BW_PATH_MEDIA . DS . 'baseweb' .DS. 'imagens' .DS. 'logo.jpg');
define('BW_URL_LOGO', BW_URL_MEDIA .'/baseweb/imagens/logo.jpg');

?>
