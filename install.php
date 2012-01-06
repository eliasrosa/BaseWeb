<?
define('DS', DIRECTORY_SEPARATOR);
define('BW', true);
define('BW_INSTALL', true);
define('BW_PATH', dirname(__FILE__));
define('BW_PATH_CONFIG', BW_PATH .DS. 'config.php');
define('BW_PATH_UPGRADE_FILES', BW_PATH .DS. 'install' .DS. 'upgrade');
define('BW_PATH_UPGRADE_SQL', BW_PATH .DS. 'install' .DS. 'sql');

require('libraries/core/install.php');

bwInstall::init();
?>
