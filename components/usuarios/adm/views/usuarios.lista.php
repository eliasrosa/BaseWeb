<?
defined('BW') or die("Acesso negado!");

echo bwAdm::createHtmlSubMenu(1);
echo bwButton::redirect('Criar novo usuário', 'adm.php?com=usuarios&view=cadastro');

function grid_col0($i){ return '<a href="' . bwRouter::_('adm.php?com=usuarios&view=cadastro&id=' . $i->id) . '">'.$i->id.'</a>'; }
function grid_col1($i){ return '<a href="' . bwRouter::_('adm.php?com=usuarios&view=cadastro&id=' . $i->id) . '">'.$i->user.'</a>'; }
function grid_col2($i){ return $i->nome; }
function grid_col3($i){ return '<a href="' . bwRouter::_('adm.php?com=usuarios&sub=grupos&view=cadastro&id=' . $i->Grupo->id) . '">'.$i->Grupo->nome.'</a>'; }
function grid_col4($i){ return bwUtil::data($i->dataLastVisit); }
function grid_col5($i){ return bwUtil::data($i->dataRegistro); }
function grid_col6($i){ return bwUtil::data($i->lastIp); }

$a = new bwGrid();
$a->setQuery(Doctrine_Query::create()->from('Usuario u')->innerJoin('u.Grupo g'));
$a->addCol('ID', 'u.id', 'tac', 50);
$a->addCol('Usuário', 'u.user', 'tac', 100);
$a->addCol('Nome', 'u.nome');
$a->addCol('Grupo', 'g.nome', 'tac', 150);
$a->addCol('Último acesso', 'u.dataLastVisit', 'tac', 150);
$a->addCol('Cadastrado', 'u.dataRegistro', 'tac', 150);
$a->addCol('Último IP', 'u.lastIp', 'tac', 150);
$a->show();

?>




