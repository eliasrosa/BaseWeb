<?
defined('BW') or die("Acesso negado!");

$tituloPage = "Administração de Usuários";

$menu = array(
    '0' => array(
        'url' => 'adm.php?com=usuarios&view=perfil',
        'tit' => 'Perfil'
    ),
    '1' => array(
        'url' => 'adm.php?com=usuarios&view=lista',
        'tit' => 'Usuários'
    ),
    '2' => array(
        'url' => 'adm.php?com=usuarios&sub=grupos&view=lista',
        'tit' => 'Grupos e Permissões'
    )
);

?>
