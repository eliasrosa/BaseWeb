<?
define('BW', 1);
define('DS', DIRECTORY_SEPARATOR);
define('BW_PATH', dirname(__FILE__));

@require_once(BW_PATH . DS . 'config.php');
@require_once(BW_PATH . DS . 'libraries' . DS . 'defines.php');
@require_once(BW_PATH . DS . 'libraries' . DS . 'loader.php');

header("Content-Type: text/plain;charset=utf8");
?>
Sitemap: <?= BW_URL_BASE2 ?>/sitemap.xml
User-agent: *
Disallow: /adm/
Disallow: /cache/
Disallow: /components/
Disallow: /libraries/
Disallow: /media/
Disallow: /modules/
Disallow: /plugins/
Disallow: /templates/
Disallow: /material/
Disallow: /install/
Disallow: /scripts/
Disallow: /nbproject/
