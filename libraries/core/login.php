<?

defined('BW') or die("Acesso negado!");

class bwLogin extends bwObject
{

    // Config
    var $config = false;
    // Mensagens
    var $mensagens = array();
    // Usuário Atual
    var $usuario = false;

    // __construct
    function __construct()
    {
        parent::__construct();

        // config
        $this->config = new bwConfigDB();

        // messagens
        $this->mensagens['site.offline'] = $this->config->getValue('core.site.offline.mensagem');
        $this->mensagens['adm.offline'] = $this->config->getValue('core.adm.offline.mensagem');
        $this->mensagens['permissao'] = 'Você não tem permissão \'ADM!\'';
        $this->mensagens['invalido'] = 'Usuário ou senha inválido!';
        $this->mensagens['grupo'] = 'Usuário ou senha inválido, tente novamente!';
    }

    // getInstance
    public function getInstance($class = false)
    {
        $class = $class ? $class : __CLASS__;
        return bwObject::getInstance($class);
    }

    // verifica login
    public function entrar($user, $pass, $auto_redirect = false)
    {
        //
        if ($user && $pass) {
            // login pelo e-mail
            if (bwUtil::isEmail($user)) {
                $email = $user;
                $dql = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.status = 1 AND u.email = ?', $email)
                    ->fetchOne();

                if ($dql)
                    $user = $dql->user;
                else {
                    $this->setMsg('invalido');
                    return false;
                }
            }

            // cria o pass criptografada com user
            $pass = $this->gerarSenha($user, $pass);

            // sql
            $dql = Doctrine_Query::create()
                ->from('Usuario u')
                ->innerJoin('u.Grupo g')
                ->where('u.status = 1 AND u.user = ? AND u.pass = ?', array($user, $pass))
                ->fetchOne();

            // verifica
            if ($dql) {
                // verifica se o grupo esta ativo
                if ($dql->Grupo->status) {

                    // verifica se o adm esta em manutencão
                    if ($dql->Grupo->isAdm && $this->config->getValue('core.adm.offline')) {
                        return $this->setMsg('adm.offline');
                    }

                    // atualiza o banco
                    $dql->lastSessionId = bwSession::getToken();
                    $dql->dataLastVisit = bwUtil::dataNow();
                    $dql->lastIp = bwUtil::getIpReal();
                    $dql->save();

                    // salva session
                    $this->setSession($dql);

                    // redireciona 
                    if ($auto_redirect) {
                        bwUtil::redirect($auto_redirect);
                    }

                    return true;
                } else
                    $this->setMsg('grupo');
            } else
                $this->setMsg('invalido');
        } else
            $this->setMsg('invalido');

        return false;
    }

    // grava session
    private function setSession($u)
    {
        return bwSession::set('login', $u->toArray());
    }

    // pega session
    public function getSession()
    {
        if ($this->isLogin()) {
            $u = bwSession::get('login', false);
            return bwUtil::array2object($u);
        }

        return false;
    }

    // verifica se ta logado
    public function isLogin()
    {
        return count(bwSession::get('login', array())) ? true : false;
    }

    // logoff
    public function sair()
    {
        bwSession::destroy();
    }

    // gera a senha para o banco
    private function gerarSenha($user, $pass)
    {
        $pass1 = "baseweb://{$user}:{$pass}";
        $pass2 = sha1(md5($pass1));

        return $pass2;
    }

    // seta o erro
    private function setMsg($id)
    {
        bwSession::del('login');
        $this->mensagemID = $id;
        return;
    }

    // retorna a mensagem
    public function mostrarMensagem()
    {
        return $this->mensagens[$this->mensagemID];
    }

    // retorna o id do usuario logado
    public function getId()
    {
        $session = $this->getSession();
        $id = $session !== false ? $session->id : 0;

        return $id;
    }

    //
    public function restrito($is_grupo_adm, $url_login)
    {
        $view = BW_URL_BASE2 . bwRequest::getVar('view');
        $url_login = bwRouter::_($url_login);

        if($view == $url_login){
            $this->sair();
            return;
        }

        if ($this->isLogin()) {

            // pega o usuário
            $u = $this->getSession();

            // mostra o login se o usuário tentar acessar o ADM e o grupo não for ADM
            if ($is_grupo_adm && $u->Grupo->isAdm == 0) {
                $this->sair();
                bwUtil::redirect($url_login.'?m=noadm', false);
            }

            // dados da session
            $ip = bwUtil::getIpReal();
            $session = bwSession::getToken();

            $dql = Doctrine_Query::create()
                ->from('Usuario u')
                ->innerJoin('u.Grupo g')
                ->where('u.status = 1')
                ->andWhere('u.id = ?', $u->id)
                ->andWhere('u.user = ?', $u->user)
                ->andWhere('u.pass = ?', $u->pass)
                ->andWhere('u.idgrupo = ?', $u->idgrupo)
                ->andWhere('u.lastIp = ?', $ip)
                ->andWhere('u.lastSessionId = ?', $session)
                ->fetchOne();

            // update log
            if ($dql) {
                $dql->dataLastVisit = bwUtil::dataNow();
                $dql->save();
            } else {
                $this->sair();
                bwUtil::redirect($url_login.'?m=expired', false);
            }

            return;
        }

        $this->sair();
        bwUtil::redirect($url_login, false);
    }

}

?>
