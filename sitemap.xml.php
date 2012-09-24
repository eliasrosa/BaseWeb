<?php

define('BW', 1);
define('DS', DIRECTORY_SEPARATOR);
define('BW_PATH', dirname(__FILE__));

@require_once(BW_PATH . DS . 'config.php');
@require_once(BW_PATH . DS . 'libraries' . DS . 'defines.php');
@require_once(BW_PATH . DS . 'libraries' . DS . 'loader.php');

// init
bwCore::setUtf8();
bwSession::init();
$template = bwTemplate::getInstance();
bwRouter::load();

header("Content-Type: text/xml;charset=utf8");

function addUrl($url, $priority = false, $changefreq = 'monthly')
{
    $url = bwRouter::_($url);

    echo "\t<url>\n";
    echo "\t\t<loc>" . $url . "</loc>\n";
    echo "\t\t<changefreq>" . $changefreq . "</changefreq>\n";

    if ($priority)
        echo "\t\t<priority>" . $priority . "</priority>\n";

    echo "\t</url>\n";
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

addUrl('/', '1.0');

foreach (bwRouter::getRoutes() as $k => $router) {
    $is_fields = count($router['fields']);

    if($router['sitemap'] === true) {
        $view_file = $template->getPathHtml() . $k . '.php';
        $view_folder = $template->getPathHtml() . $k . '/index.php';
        $view_exist = (bwFile::exists($view_file) || bwFile::exists($view_folder));

        if ($view_exist && !$is_fields) {
            addUrl($k, '0.5');
        }

        if ($view_exist && $is_fields) {
            $priority = '0.5';
            $dynamic_sitemap_file = $template->getPath() . '/xml' . $k . '.php';
            if (bwFile::exists($dynamic_sitemap_file)) {
                require ($dynamic_sitemap_file);
                if (count($dql)) {
                    foreach ($dql as $i) {
                        addUrl($i->getUrl($k), $priority);
                    }
                }
            }
        }
    }
}

echo "</urlset>";

