<?

defined('BW') or die("Acesso negado!");

echo bwAdm::createHtmlSubMenu(1);
echo bwButton::redirect('Criar novo usuário', '/usuarios/cadastro/0');

function grid_col0($i)
{
    return '<a href="' . $i->getUrl('/usuarios/cadastro') . '">' . $i->id . '</a>';
}

function grid_col1($i)
{
    return $i->nome;
}

function grid_col2($i)
{
    return $i->email;
}

function grid_col3($i)
{
    return $i->Grupo->nome;
}

function grid_col4($i)
{
    return bwUtil::data($i->dataLastVisit);
}


$a = new bwGrid();
$a->setQuery(Doctrine_Query::create()->from('Usuario u')->innerJoin('u.Grupo g'));
$a->addCol('ID', 'u.id', 'tac', 50);
$a->addCol('Nome', ' u.nome');
$a->addCol('E-mail', 'u.email', NULL, 280);
$a->addCol('Grupo', 'g.nome', 'tac', 150);
$a->addCol('Último acesso', 'u.dataLastVisit', 'tac', 150);
$a->show();
?>




