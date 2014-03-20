<?
defined('BW') or die("Acesso negado!");

bwAdm::init('/login');

//
bwHtml::setTitle("Administração");

//
$template = bwTemplate::getInstance();
$login = bwLogin::getInstance();

//
$view = str_replace('/adm/', '', bwRequest::getVar('view'));
list($com, $view) = explode('/', $view, 2);
?>

<!DOCTYPE html>
<html>
    <head>              

        <!-- jQuery e jQuery UI -->
        <link type="text/css" href="<?= BW_URL_JAVASCRIPTS ?>/jquery/themes-1.8rc2/redmond/style.css" rel="Stylesheet" />
        <script type="text/javascript" src="<?= BW_URL_JAVASCRIPTS ?>/jquery/jquery-1.7.1.min.js"></script> 
        <script type="text/javascript" src="<?= BW_URL_JAVASCRIPTS ?>/jquery/ui-1.8rc2.js"></script> 

        {BW HEAD}

        <!-- Head da página -->
        <link type="text/css" href="<?= $template->getUrl() ?>/css/styles.css" rel="Stylesheet" />
        <script type="text/javascript" src="<?= $template->getUrl() ?>/js/comum.js"></script>       

        <!--[if lte IE 8]>
            <link type="text/css" href="<?= $template->getUrl() ?>/css/style-ie.css" rel="Stylesheet" />
        <![endif]-->

    </head>
    <body>

        <div id="page">

            <div id="topo">
                <div class="center">

                    <h1>BW - PHP Framework</h1>

                    <div class="dados">
                        <a href="#"><?= $login->getSession()->email; ?></a> |
                        <a href="<?= BW_URL_BASE2 ?>" target="_blank">Visualizar site</a> |
                        <a href="<?= bwRouter::_('/config'); ?>">Configurações</a> | 
                        <a class="sair" href="<?= bwRouter::_('/sair'); ?>">Sair</a>
                    </div>

                    <select class="componentes" onchange="window.location.href=this.options[this.selectedIndex].value">
                        <option>-- Selecione um componente --</option>
                        <?
                        foreach (bwComponent::getAll() as $c) {
                            if ($c['adm_visivel']) {
                                echo sprintf('<option value="%s">%s</option>'
                                    , bwRouter::_('/' . $c['id'])
                                    , $c['nome']
                                );
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div id="menu">

                <a href="#" class="prev">Scroll Left</a>

                <div class="center">
                    <ul class="com">
                        <?
                        foreach (bwComponent::getAll() as $c) {
                            if ($c['adm_visivel']) {
                                $active = ($com == $c['id']) ? ' active' : '';
                                $class = "{$c['id']}{$active}";
                                echo sprintf('<li class="%s"><a href="%s">%s</a></li><li>|</li>'
                                    , $class
                                    , bwRouter::_('/' . $c['id'])
                                    , $c['nome']
                                );
                            }
                        }
                        ?>
                    </ul>
                </div>


                <a href="#" class="next">Scroll Right</a>    

            </div>      

            <div id="conteudo">
                <div class="center">
                    {BW VIEW}
                </div>
            </div>

            <br class="clearfix"></br>

            <div id="rodape">
                <div class="center">
                    <p>BW - PHP Framework | <a href="http://github.com/eliasrosa/baseweb" target="_blank">http://github.com/eliasrosa/baseweb</a></p>
                </div>
            </div>
    </body>
</html>
