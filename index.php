<?php
define('BW', 1);
define('DS', DIRECTORY_SEPARATOR);
define('BW_PATH', dirname(__FILE__));

require_once(BW_PATH .DS. 'config.php');
require_once(BW_PATH .DS. 'libraries' .DS. 'defines.php');
require_once(BW_PATH .DS. 'libraries' .DS. 'loader.php');

// inicia a mágica!!!
bwCore::init();
?>
