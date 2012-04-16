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
    function  __construct()
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

    // verifica login
    public function entrar()
    {
        // check is post
        if (bwRequest::getMethod() != 'POST')
            return;

        // check Token
        if (!bwRequest::checkToken())
            return $this->setMsg('token');

        // user
        $user = bwRequest::getVar('user', false);
        $pass = bwRequest::getVar('pass', false);

        //
        if ($user && $pass)
        {
            // login pelo e-mail
            if(bwUtil::isEmail($user))
            {
                $email = $user;
                $dql = Doctrine_Query::create()
                    ->from('Usuario u')
                    ->where('u.status = 1 AND u.email = ?', $email)
                    ->fetchOne();
                    
                if($dql)
                    $user = $dql->user;
                else
                    return $this->setMsg('invalido');
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
            if ($dql)
            {
                // verifica se o grupo esta ativo
                if ($dql->Grupo->status)
                {
                    // verifica se o adm esta em manutencão
                    if ($dql->Grupo->isAdm && $this->config->getValue('core.adm.offline'))
                        return $this->setMsg('adm.offline');

                    // atualiza o banco
                    $dql->lastSessionId = bwSession::getToken();
                    $dql->dataLastVisit = bwUtil::dataNow();
                    $dql->lastIp = bwUtil::getIpReal();
                    $dql->save();
                    
                    // salva session
                    $this->setSession($dql);

                    // redireciona 
                    $redirect = bwRequest::getVar('redirect');
                    
                    // NÃO tem premissão ao ADM
                    if(!$dql->Grupo->isAdm &&  preg_match('#^' . BW_URL_ADM . '/.*#', base64_decode($redirect)))
                    {
                        return $this->setMsg('permissao');
                    }
                    elseif (!empty($redirect))
                    {
                        bwUtil::redirect(base64_decode($redirect), false);
                    }
                    elseif(empty($redirect) && $dql->Grupo->isAdm)
                    {
                        bwUtil::redirect(BW_URL_ADM, false);
                    }
                    else
                    {
                        bwUtil::redirect(BW_URL_BASE2, false);
                    }
                    
                    return $this->setMsg('adm.offline');
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
    public function setSession($u)
    {
        return bwSession::set('login', $u->toArray());
    }

    // pega session
    function getSession()
    {
        if($this->isLogin())
        {
            $u = bwSession::get('login', false);
            return bwUtil::array2object($u);
        }
        
        return false;
    }

    // verifica se ta logado
    function isLogin()
    {
        return count(bwSession::get('login', array())) ? true : false;
    }

    // logoff
    public function sair()
    {
        bwSession::destroy();
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
    function restrito($isAdm = false, $loginUrl = NULL, $activeRedirect = true)
    {
        $urlAtual = new bwUrl();
        
        // url login
        $loginUrl = is_null($loginUrl) ? BW_URL_ADM_LOGIN_FILE : $loginUrl;
        $urlLogin = new bwUrl($loginUrl);
        
        if($activeRedirect)
          $urlLogin->setVar('redirect', $urlAtual->toBase64());
        
        if (!$this->isLogin() && $urlAtual->getPath() != $urlLogin->getPath())
        {
            $this->sair();
            bwUtil::redirect($urlLogin->toString(), false);
        }    
        else
        {
            // pega o usuário
            $u = $this->getSession();

            // mostra o login se o usuário tentar acessar o ADM e o grupo não for ADM
            if($isAdm && $u->Grupo->isAdm == 0)
            {
              $this->sair();
              bwUtil::redirect($urlLogin->toString(), false);
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

            //
            if (!$dql && $urlAtual->getPath() != $urlLogin->getPath())
            {
                $this->sair();
                bwUtil::redirect($urlLogin->toString(), false);
            }

            // update log
            if ($dql)
            {
                $dql->dataLastVisit = bwUtil::dataNow();
                $dql->save();
            }
        }    
    }

}
?>
