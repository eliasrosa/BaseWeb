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



bwRouter::addUrl('/galeria/album');
bwRouter::addUrl('/galeria/upload');
bwRouter::addUrl('/galeria/imagens');
bwRouter::addUrl('/galeria/imagem');
bwRouter::addUrl('/galeria/ordem');
bwRouter::addUrl('/galeria/remover');
bwRouter::addUrl('/galeria/salvar');