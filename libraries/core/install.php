<?
defined('BW') or die("Acesso negado!");

class bwInstall
{
    public function init()
    {
        // loader
        require_once(BW_PATH .DS. 'libraries' .DS. 'defines.php');    
        require(BW_PATH_LIBRARIES .DS. 'core' .DS. 'loader.php');

        // auto load
        bwLoader::import('doctrine.doctrine');

        // inicia o auto load
        spl_autoload_register('bwLoader::autoload');

        // arquivos importantes
        bwLoader::import('core.functions');   
    
        if(!bwInstall::is_exists_config())
        {
            bwInstall::show_upgrade('config'); 
        }
        else
        {
            require(BW_PATH_CONFIG);
            bwLoader::import('core.conexao');
            
            $versaoAtual = bwCore::getVersion();
            
            //
            bwInstall::show_upgrade($versaoAtual);
        }    
    }

    public function show_upgrade($versaoAtual)
    {
        if($versaoAtual != 0 && (bwUtil::getIpReal() != bwConfig::$allowUpdate))
            bwInstall::show_page_error("Acesso negado!<br/><br/>IP:". bwUtil::getIpReal());
    
        $file = BW_PATH_UPGRADE_FILES .DS. $versaoAtual .'.php';
        if(!file_exists($file) && $versaoAtual != 'config')
            bwInstall::show_page_error("Sua versão está instalada e/ou atualizada!", "Parabéns!");
        else
            require($file);
        
        if(method_exists(bwUpgrade, 'show_form') && empty($_POST))  
        {          
            bwInstall::show_header_html();
            bwUpgrade::show_form();
            bwInstall::show_submit_html();
            bwInstall::show_footer_html();    
        }
        elseif(!method_exists(bwUpgrade, 'show_form') && empty($_POST))  
        {          
            bwInstall::show_page_update($versaoAtual);
        }
        elseif(!empty($_POST))
        {
            bwUpgrade::execute($versaoAtual);
        }
    }

    public function show_page_error($html, $titulo = "Erro")
    {
        bwInstall::show_header_html();
        bwInstall::show_error($html, $titulo);
        bwInstall::show_footer_html();
        exit();    
    }

    public function show_page_update($versaoAtual)
    {
        bwInstall::show_header_html();
        bwInstall::show_error("", "Atualização #$versaoAtual");
        bwInstall::show_submit_html();
        bwInstall::show_footer_html();
        exit();    
    }

    public function show_header_html()
    {
        echo <<<TOP
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>              
        <title>Instalação/Upgrade do BaseWeb</title>
        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="libraries/javascripts/jquery/jquery-1.6.min.js"></script> 
        <script type="text/javascript" src="libraries/javascripts/validaform/jquery.meio.mask.min.js"></script> 
        <script type="text/javascript" src="libraries/javascripts/validaform/jquery.validaform.js"></script> 
        <link type="text/css" href="install/css/style.css" rel="Stylesheet" />
        <script type="text/javascript" src="install/js/comum.js"></script> 
    </head>
    <body>  
        <div class="page">
            <div class="logo">
                <img src="install/img/logo.jpg" />
            </div>
            <form action="" class="validaForm" method="post">
TOP;
    }

    public function show_submit_html()
    {
        echo "<p class=\"instalando-msg\">Instalando...</p><input class=\"sub\" type=\"submit\" name=\"__submit__\" value=\"Instalar!\" />";
    }
    
    public function show_footer_html()
    {
        echo "</form></div></body></html>";
    }

    public function show_error($html, $titulo = "Erro")
    {
        echo "<div class=\"error\"><h2>$titulo</h2>$html</div>";
    }

    public function is_exists_config()
    {
        return file_exists(BW_PATH_CONFIG);
    }

    public function is_installed()
    {
        return file_exists(BW_PATH_CONFIG);
    }
}
?>
