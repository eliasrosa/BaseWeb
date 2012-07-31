<?

defined('BW') or die("Acesso negado!");


$menu = array(
    'core.site' => array(
        'url' => '/config/?tab=core.site',
        'tit' => 'Site (básico)'
    ),
    'core.adm' => array(
        'url' => '/config/?tab=core.adm',
        'tit' => 'Administração'
    ),
    'core.seo' => array(
        'url' => '/config/?tab=core.seo',
        'tit' => 'SEO'
    ),
    'plugins' => array(
        'url' => '/config/?tab=plugins',
        'tit' => 'Plugins'
    ),
    'core.cache' => array(
        'url' => '/config/?tab=core.cache',
        'tit' => 'Cache'
    ),
    'core.debug' => array(
        'url' => '/config/?tab=core.debug',
        'tit' => 'Debug'
    ),
);

echo sprintf('<div class="sm01" id="submenu">');
echo sprintf('<h2>Configurações do sistema</h2>');
echo sprintf('<ul>');

$tab = bwRequest::getVar('tab', 'core.site');

foreach($menu as $k=>$m) {
    $class = ($tab == $k) ? ' active' : '';
    echo sprintf('<li class="item%s"><a href="%s">%s</a></li>', $class, bwRouter::_($m['url']), $m['tit']);
}

echo sprintf('</ul>');
echo sprintf('</div>');

$config = new bwConfigDB();
$config->setPrefix($tab);
$config->createHtmlPainel();
