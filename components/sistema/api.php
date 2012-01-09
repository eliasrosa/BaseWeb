<?

defined('BW') or die("Acesso negado!");

class bwSistema extends bwComponent
{
    // variaveis obrigatórias
    var $id = 'sistema';
    var $nome = 'Sistema';
    var $adm_url_default = '';
    var $adm_visivel = false;
    
    
    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
}
?>
