<?
defined('BW') or die("Acesso negado!");

$tituloPage = "Administração de Usuários";

$menu = array(
    '1' => array(
        'url' => '/usuarios/lista',
        'tit' => 'Usuários'
    ),
    '2' => array(
        'url' => '/usuarios/grupos/lista',
        'tit' => 'Grupos e Privilégios'
    )
);

?>
