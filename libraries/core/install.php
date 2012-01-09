<?
defined('BW') or die("Acesso negado!");

class bwInstall
{
    public function __construct($params)
    {
        echo "\n";
        $opcoes = @$params[2];
        
        unset($params[0], $params[1], $params[2]);
        $this->params = $params;
        
        switch($opcoes)
        {
            case 'create-config':
                $this->createConfig(
                    $this->getParms('h', 'localhost'),
                    $this->getParms('u', 'root'),
                    $this->getParms('s', ''),
                    $this->getParms('p', '3306'),
                    $this->getParms('b', 'baseweb'),
                    0
                );
                break;

            case 'update':
                $this->loadConfig();
                
                do{
                    $versao = bwConfig::$versao + 1;
                    $method = "bwUpdate_{$versao}";
                    
                    if(method_exists($this, $method))
                        $this->update();
                    else
                        break;
                        
                } while(true);
                break;

            case '--help':
                echo "Lista de opções disponíves";

                echo "\n\n";
                echo "  update         - Executa as funções de atualização do sistema";
               
                echo "\n\n";
                echo "  update-com     - Atualiza componente selecionado\n";
                echo "    <opções>\n";
                echo "      --c  Pasta do componente, Ex.: --c=noticias";
                
                echo "\n\n";
                echo "  create-config  - Cria arquivo config.php\n";
                echo "    <opções>\n";
                echo "      --h  Servidor MySql [localhost]\n";
                echo "      --b  Nome do banco de dados MySql [baseweb]\n";
                echo "      --u  Usuário MySql [root]\n";
                echo "      --s  Senha MySql, Ex.: --s=\"senha do banco\"\n";
                echo "      --p  Porta MySql [3306]\n";
                echo "    <exemplos>\n";
                echo "      $ bw install create-config\n";
                echo "      $ bw install create-config --h=10.1.1.5 --u=root --s=\"123\" --b=baseweb";
                
                break;

            default:
                echo "Para informações, use: bw install --help";
                break;
        }
        echo "\n\n";
        die();
    }
    
    private function getParms($var, $default = '')
    {
        if(count($this->params))
        {
            foreach($this->params as $p)
            {
                if(preg_match("#^--{$var}=(.*)$#", $p, $r))
                {
                    return $r[1];
                }
            }
        }
        
        return $default;
    }
    
    private function createConfig($h, $u, $s, $p, $b, $v)
    {
        require_once(BW_PATH .DS. 'libraries' .DS. 'defines.php');
        require_once(BW_PATH_LIBRARIES .DS. 'core' .DS. 'config.php');
          
        $c = new bwConfigCreatorClass('bwConfig', BW_PATH_CONFIG);
        $c->set('db_host', $h);
        $c->set('db_user', $u);
        $c->set('db_pass', $s);
        $c->set('db_port', $p);
        $c->set('db_name', $b);
        $c->set('versao', $v);
       
        if(@mysql_connect($c->get('db_host').':'.$c->get('db_port'), $c->get('db_user'), $c->get('db_pass')))
        {
            if(@mysql_select_db($c->get('db_name')))
            {
                $c->create();
                echo "Aquivo config.php criado com sucesso!";
            }
            else
               echo "Não foi possível encontrar o banco de dados '".$c->get('db_name'). "'";
        }
        else
            echo "Não foi possível conectar ao banco de dados";
    }
    
    private function loadConfig()
    {
        if(isset($this->config))
            return;
    
        // loader
        require_once(BW_PATH_CONFIG);    
        require_once(BW_PATH .DS. 'libraries' .DS. 'defines.php');    
        require_once(BW_PATH_LIBRARIES .DS. 'core' .DS. 'loader.php');

        // auto load
        bwLoader::import('doctrine.doctrine');

        // inicia o auto load
        spl_autoload_register('bwLoader::autoload');

        // arquivos importantes
        bwLoader::import('core.functions');
        
        //
        $this->config = true;
    }

    private function is_exists_config()
    {
        return bwFile::exists(BW_PATH_CONFIG);
    }

    private function update()
    {
        $this->loadConfig();
        
        $versao = bwConfig::$versao + 1;
        $method = "bwUpdate_{$versao}";

        if(method_exists($this, $method))
        {
            echo "Executando update #{$versao}\n";
            $r = call_user_func(array($this, $method));
        
            $this->createConfig(
                bwConfig::$db_host,
                bwConfig::$db_user,
                bwConfig::$db_pass,
                bwConfig::$db_port,
                bwConfig::$db_name,
                $versao
            );
            
            bwConfig::$versao = $versao;
            
            echo "\n";
            echo "Atualização concluída com sucesso!\n\n";
        }
    }

    private function showProgressMsg($msg)
    {
        echo "  - $msg\n";
    }

    private function bwUpdate_0()
    {
        //bwCore::getConexao()->exec();
    }
}
?>
