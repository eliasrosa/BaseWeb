<?
defined('BW') or die("Acesso negado!");

class bwUpgrade
{
    function show_form()
    {
        echo <<<FORM
        
            <!--
            <h2>Licença</h2>
            <input class="w1" type="text" name="serial" value="" title="Licença" rel="text_"  />
            -->

            <h2>Conexão com banco de dados</h2>
            <label>Host</label>
            <input class="w3" type="text" name="host" value="localhost" title="Host" rel="text_"  />

            <label>Nome do banco</label>
            <input class="w3" type="text" name="banco" value="" title="Nome do banco" rel="text_"  />
            
            <label>Usuário</label>
            <input class="w3" type="text" name="user" value="" title="Usuário" rel="text_"  />

            <label>Senha</label>
            <input class="w3" type="text" name="pass" value=""  />

            <label>Porta</label>
            <input class="w2" type="text" name="porta" value="3306" title="Porta" rel="int_"  />
FORM;

    }

    function execute($versaoAtual)
    {
        require(BW_PATH_LIBRARIES .DS. 'core' .DS. 'config.php');
        
        $c = new bwConfigCreatorClass('bwConfig', BW_PATH_CONFIG);
        $c->set('db_host', $_POST['host']);
        $c->set('db_user', $_POST['user']);
        $c->set('db_pass', $_POST['pass']);
        $c->set('db_port', $_POST['porta']);
        $c->set('db_name', $_POST['banco']);
        //$c->set('serial', $_POST['serial']);
        $c->set('allowUpdate', bwUtil::getIpReal());
        $c->set('install_url', str_replace('/install.php', '', $_SERVER['SCRIPT_NAME']));
       
        @mysql_connect($c->get('db_host').':'.$c->get('db_port'), $c->get('db_user'), $c->get('db_pass')) or die('Não foi possível conectar ao banco de dados');
        @mysql_select_db($c->get('db_name')) or die ('Não foi possível encontrar o banco de dados "'.$c->get('db_name').'"');
        
        $c->create();
    }
}
?>
