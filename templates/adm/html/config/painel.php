<?
defined('BW') or die("Acesso negado!");

$url = new bwUrl();

$dql = Doctrine_Query::Create()
    ->from('Configuracao c')
    ->where("c.var LIKE '{$prefix}%' AND c.oculto = 0")
    ->orderBy("c.titulo ASC")
    ->execute();


if ($dql->count()) {
    $menus = array();
    foreach ($dql as $m) {
        $url->setVar('var', $m->var);

        $tit = $m->titulo == '' ? $m->var : $m->titulo;
        $menus[$m->var] = array(
            'tit' => $tit,
            'url' => $url->toString(),
        );
    }

    $var = bwRequest::getVar('var', key($menus), 'get');
    $tab = $menus[$var];
    ?>
    <table id="config-painel">
        <tr>
            <td class="lateral-menu">
                <ul>
                        <?
                        foreach ($menus as $k => $v) {
                            $class = ($var == $k) ? ' class="active"' : '';
                            echo '<li' . $class . '><a href="' . $v['url'] . '">' . $v['tit'] . '</a></li>';
                        }
                        ?>
                </ul>
            </td>
            <td class="container">
                <h2><?= str_replace('Config. ', 'Configurações ', $tab['tit']); ?></h2>
                <div class="conteudo">
                        <?
                        $config = new bwConfigDB();
                        $config->setPrefix($prefix);
                        $config->createHtmlForm($var);
                        ?>
                </div>
            </td>
        </tr>
    </table>
    <?
}else
    echo bwAdm::msg('Variáveis de configuração não encontrada!', 1);
?>