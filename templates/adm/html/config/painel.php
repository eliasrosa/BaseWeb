<?
defined('BW') or die("Acesso negado!");

$url = new bwUrl();

$dql = Doctrine_Query::Create()
                ->from('Configuracao c')
                ->where("c.var LIKE '{$prefix}%' AND c.oculto = 0")
                ->orderBy("c.titulo ASC")
                ->execute();


if ($dql->count())
{
    $menus = array();
    foreach ($dql as $m)
    {
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
    <table class="painel" id="painel-lateral">
        <tr>
            <td class="lateral-menu">
                <h2>Selecione uma opção</h2>

                <ul>
                <?
                foreach ($menus as $k => $v)
                {
                    $class = ($var == $k) ? ' class="active"' : '';
                    echo '<li' . $class . '><a href="' . $v['url'] . '">' . $v['tit'] . '</a></li>';
                }
                ?>
            </ul>
        </td>
        <td>
            <h2 class="header"><?= str_replace('Config. ', 'Configurações ', $tab['tit']);
                ?></h2>
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