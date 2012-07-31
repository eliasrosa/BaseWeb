<?php
define('BW', 1);
define('DS', DIRECTORY_SEPARATOR);
define('BW_PATH', dirname(__FILE__) .DS. '..' .DS. '..');

require_once(BW_PATH .DS. 'config.php');
require_once(BW_PATH .DS. 'libraries' .DS. 'defines.php');

// loader
require(BW_PATH_LIBRARIES .DS. 'core' .DS. 'loader.php');

// auto load
bwLoader::import('doctrine.doctrine');

// inicia o auto load
spl_autoload_register('bwLoader::autoload');

// arquivos importantes
bwLoader::import('core.functions');
bwLoader::import('core.conexao');

// inicia a session
$login = new bwLogin();
$login->sair();

// redireciona ao login
bwUtil::redirect(BW_URL_ADM_LOGIN);	
?>

