<?

defined('BW') or die("Acesso negado!");

class bwPhp extends bwComponent
{
    // variaveis obrigatórias
    var $id = 'php';
    var $nome = 'PHP';
    var $adm_url_default = '';
    var $adm_visivel = false;

    
    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    private $path = null;
    private $url = null;

    function __construct()
    {
        parent::__construct();

        $this->setPath();
        $this->setUrl();
    }

    public function setPath()
    {
        $this->path = bwTemplate::getInstance()->getPathHtml() . DS . 'com_php';
    }

    public function setUrl()
    {
        $this->url = bwTemplate::getInstance()->getUrlHtml() . '/com_php';
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getPathFileView($page)
    {
        return $this->getPath() . DS . 'view' . DS . $page . '.php';
    }

    public function getPathFileMod($page)
    {
        return $this->getPath() . DS . 'mod' . DS . $page . '.php';
    }

    public function getPathFilePlugin($page)
    {
        return $this->getPath() . DS . 'plugin' . DS . $page . '.php';
    }

}
?>
