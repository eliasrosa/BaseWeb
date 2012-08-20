<?

defined('BW') or die("Acesso negado!");

echo bwAdm::createHtmlSubMenu(2);
echo bwButton::redirect('Criar novo grupo', '/usuarios/grupos/cadastro/0');

function grid_col0($i)
{
    return '<a href="' . $i->getUrl('/usuarios/grupos/cadastro') . '">' . $i->id . '</a>';
}

function grid_col1($i)
{
    return $i->nome;
}

function grid_col2($i)
{
    return $i->descricao;
}

function grid_col3($i)
{
    return bwAdm::getImgStatus($i->isAdm);
}

$a = new bwGrid();
$a->setQuery(Doctrine_Query::create()->from('UsuarioGrupo'));
$a->addCol('ID', 'id', 'tac', 50);
$a->addCol('Nome', ' nome');
$a->addCol('Descrição', 'descricao', NULL, '40%');
$a->addCol('Acesso Administrador', 'isAdm', 'tac', 100);
$a->show();
?>




