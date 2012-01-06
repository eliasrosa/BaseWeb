<?

defined('BW') or die("Acesso negado!");

class bwLogin extends bwObject
{
    // Config
    var $config = false;

    // Mensagens
    var $mensagens = array();

    //
    function  __construct()
    {
        parent::__construct();

        // config
        $this->config = new bwConfigDB();
        
        // messagens
        $this->mensagens['site.offline'] = $this->config->getValue('core.site.offline.mensagem');
        $this->mensagens['adm.offline'] = $this->config->getValue('core.adm.offline.mensagem');
        $this->mensagens['invalido'] = 'Usuário ou senha inválido!';
        $this->mensagens['grupo'] = 'Usuário ou senha inválido, tente novamente!';
        $this->mensagens['token'] = 'Token inválido!';
        $this->mensagens['navegador'] = 'Navegador não suportado!';
    }

    // getInstance
    function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }
    
    // ID da mensagem
    var $mensagemID = false;

    // session
    private $session = 'login';

    // subsession
    private $subsession = 'dados';

    // verifica login
    public function entrar()
    {
        // check is post
        if (bwRequest::getMethod() != 'POST')
            return;

        // check Token
        if (!bwRequest::checkToken())
            return $this->setMsg('token');

        // proteção IE 6
        $b = bwBrowser::getInstance();
        if ($b->getVersion() == '6.0' && $b->getBrowser() == 'msie')
            return $this->setMsg('navegador');

        // user
        $user = bwRequest::getVar('user', false);
        $pass = bwRequest::getVar('pass', false);

        if ($user && $pass)
        {
            // key cript
            $pass = $this->gerarSenha($user, $pass);

            // sql
            $dql = Doctrine_Query::create()
                            ->from('Usuario u')
                            ->innerJoin('u.Grupo g')
                            ->where('u.status = 1 AND u.user = ? AND u.pass = ?', array($user, $pass))
                            ->fetchOne();

            if ($dql)
            {
                if ($dql->Grupo->status)
                {
                    // verifica se o adm esta em manutencão
                    if ($dql->Grupo->isAdm && $this->config->getValue('core.adm.offline'))
                        return $this->setMsg('adm.offline');

                    $dql->lastSessionId = session_id();
                    $dql->save();

                    // session
                    $this->setSession($dql->toArray());

                    // redirecionamento
                    $redirect = bwRequest::getVar('redirect', false);

                    // login liberado
                    if ($redirect !== false)
                        bwUtil::redirect($redirect);
                }
                else
                    return $this->setMsg('grupo');
            }
            else
                return $this->setMsg('invalido');
        }
        else
            return $this->setMsg('invalido');
    }

    // grava session
    public function setSession($array)
    {
        // grava na session
        bwSession::set($this->subsession, $array, $this->session);
        return;
    }

    // pega session
    function getSession()
    {
        if ($this->isLogin())
            return bwSession::get($this->subsession, false, $this->session);
        else
            return false;
    }

    // logoff
    public function sair()
    {
        // apaga a session
        bwSession::set($this->subsession, false, $this->session);
    }

    // verifica se ta logado
    function isLogin()
    {
        return bwSession::get($this->subsession, false, $this->session) ? true : false;
    }

    // gera a senha para o banco
    public function gerarSenha($user, $pass)
    {
        $pass1 = "baseweb://{$user}:{$pass}";
        $pass2 = sha1(md5($pass1));

        return $pass2;
    }

    // seta o erro
    public function setMsg($id)
    {
        $this->mensagemID = $id;
        return;
    }

    // retorna a mensagem
    public function mostrarMensagem()
    {
        return $this->mensagens[$this->mensagemID];
    }

}
?>
