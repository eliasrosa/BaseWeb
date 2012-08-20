<?

defined('BW') or die("Acesso negado!");

// configurações
bwRouter::addUrl('/config');
bwRouter::addUrl('/config/save', array('type' => 'task'));
bwRouter::addUrl('/login', array('type' => 'static'));

bwRouter::addUrl('/senha', array(
    'type' => 'static',
    'skip_constraint' => true
));

bwRouter::addUrl('/sair', array(
    'type' => 'static',
    'skip_constraint' => true
));

