<?
defined('BW') or die("Acesso negado!");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>              
                
        <!-- jQuery e jQuery UI -->
        <link type="text/css" href="<?= BW_URL_JAVASCRIPTS ?>/jquery/themes-1.8rc2/redmond/style.css" rel="Stylesheet" />
        <script type="text/javascript" src="<?= BW_URL_JAVASCRIPTS ?>/jquery/jquery-1.6.min.js"></script> 
        <script type="text/javascript" src="<?= BW_URL_JAVASCRIPTS ?>/jquery/ui-1.8rc2.js"></script> 
        
        <!-- dataTableSettings -->
        <script type="text/javascript" src="<?= BW_URL_TEMPLATE ?>/js/dataTableSettings.js"></script>
        <script type="text/javascript" src="<?= BW_URL_JAVASCRIPTS ?>/DataTables-1.7.6/media/js/jquery.dataTables.min.js"></script>
        <link type="text/css" href="<?= BW_URL_JAVASCRIPTS ?>/DataTables-1.7.6/media/css/demo_table_jui.css" rel="Stylesheet" />
        
        <!-- plugins -->
        {BW HEAD}
                
        <!-- Head da página -->
        <link type="text/css" href="<?= BW_URL_TEMPLATE ?>/css/styles.css" rel="Stylesheet" />
        <script type="text/javascript" src="<?= BW_URL_TEMPLATE ?>/js/comum.js"></script>       
        
        <!--[if lte IE 8]>
            <link type="text/css" href="<?= BW_URL_TEMPLATE ?>/css/style-ie.css" rel="Stylesheet" />
        <![endif]-->
    
    </head>
    <body>
    
        <div id="page">
                
            <? if(bwLogin::getInstance()->isLogin()): ?>
        
            <div id="top">
                <h1><?= bwCore::getConfig()->getValue('site.titulo'); ?></h1>
                <div><a href="<?= BW_URL_ADM_LOGOFF_FILE; ?>">Sair</a></div>
                <div><a href="<?= bwRouter::_('adm.php?com=sistema&view=configuracoes'); ?>">Configurações</a></div>
                <div><a href="<?= BW_URL_BASE2 ?>" target="_blank">Visualizar site <?= bwCore::getConfig()->getValue('site.titulo'); ?></a></div>
            </div>
        
            <div id="menu">
            <?        
                foreach(bwAdm::getInstance()->getMenuPrincipal() as $c)
                {
                    if($c['visivel'])
                    {
                        $active = $c['active'] ? ' active' : '';
                        $class = "{$c['com']}{$active}";
                        echo sprintf('<div class="programas %s"><a href="%s">%s</a></div>',
                            $class,
                            $c['link'],
                            $c['nome']
                        );
                    }
                }
            ?>
            </div>
            
            <div id="main">
                {BW COMPONENT}
            </div>
            
            <div id="rodape">
                <p>BaseWeb 2.0 - Desenvolvido por Elias da Rosa - <a href="http://www.eliasdarosa.com.br" target="_blank">http://www.eliasdarosa.com.br</a></p>
            </div>
            
            <? endif; ?>
            
        </div>      
    </body>
</html>
