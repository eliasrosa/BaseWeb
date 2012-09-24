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
        $this->mensagens['email.fail'] = 'E-mail inválido, tente novamente!';
        $this->mensagens['email.solicitacao'] = 'Uma mensagem foi enviada para seu e-mail!';
        $this->mensagens['email.novasenha'] = 'Uma senha foi criada e enviada para seu e-mail!';
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
    public function gerarSenha($user, $pass)
    {
        $pass1 = "baseweb://{$user}:{$pass}";
        $pass2 = sha1(md5($pass1));

        return $pass2;
    }

    /**
     * Cria uma senha aleatória
     * 
     * @param integer $tamanho Tamanho da senha a ser gerada
     * @param boolean $maiusculas Se terá letras maiúsculas
     * @param boolean $numeros Se terá números
     * @param boolean $simbolos Se terá símbolos
     *
     * @return string A senha gerada
     */
    function createSenhaRand($tamanho = 8, $maiusculas = true, $numeros = true,
        $simbolos = false)
    {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';

        $caracteres .= $lmin;
        if ($maiusculas)
            $caracteres .= $lmai;
        if ($numeros)
            $caracteres .= $num;
        if ($simbolos)
            $caracteres .= $simb;

        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand - 1];
        }

        return $retorno;
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
        $router = bwRouter::getRoute();
        if (isset($router['skip_constraint']) && $router['skip_constraint'] == true) {
            return;
        }

        $view = bwRequest::getVar('view');
        $url_atual = BW_URL_BASE2 . $view;
        $url_login = bwRouter::_($url_login);

        if ($url_atual == $url_login) {
            $this->sair();
            return;
        }

        if ($this->isLogin()) {

            // pega o usuário
            $u = $this->getSession();

            // mostra o login se o usuário tentar acessar o ADM e o grupo não for ADM
            if ($is_grupo_adm && $u->Grupo->isAdm == 0) {
                $this->sair();
                bwUtil::redirect($url_login . '?m=noadm', false);
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
                bwUtil::redirect($url_login . '?m=expired', false);
            }

            return;
        }

        $this->sair();
        bwUtil::redirect($url_login, false);
    }

    public function enviarSolitacaoSenha($email, $url, $expire = 120)
    {
        if (Usuario::findByEmail($email)) {
            $time = strtotime(sprintf('+%s minutes', $expire));
            $safe_email = bwUtil::createSafeValue($email, NULL, NULL, $time);
            $u = bwRouter::_($url . '?k=' . $safe_email);

            $html .= sprintf('<br>Olá<br><br>');
            $html .= sprintf('Recebemos uma solicitação de recuperação de senha. ');
            $html .= sprintf('Caso você não tenha solicitado, por favor ignore esta mensagem.<br><br>');
            $html .= sprintf('Para realizar este processo, clique no link abaixo:<br>');
            $html .= sprintf('<a href="%s">%s</a>', $u, $u);

            $this->sendMail($email, 'Solicitação de troca de senha', $html);
            $this->setMsg('email.solicitacao');
            return true;
        } else {
            $this->setMsg('email.fail');
            return false;
        }
    }

    public function enviarNovaSenha($email)
    {
        $u = Usuario::findByEmail($email);
        if ($u) {

            $senha = $this->createSenhaRand();
            $u->pass = $this->gerarSenha($u->user, $senha);
            $u->save();

            $html = sprintf('<br>Olá<br><br>');
            $html .= sprintf('Conforme solicitado, uma nova senha foi criada para você.<br><br>');
            $html .= sprintf('E-mail: %s<br>', $email);
            $html .= sprintf('Senha: %s<br>', $senha);
            $html .= sprintf('Site: %s<br>', BW_URL_BASE2);

            $this->sendMail($email, 'Nova senha', $html);
            $this->setMsg('email.novasenha');
            return true;
        } else {
            $this->setMsg('email.fail');
            return false;
        }
    }

    /**
     * Envia um e-mail como mail-noreplay@domain...
     * 
     * @param type $email
     * @param type $prefix_subject
     * @param type $html 
     */
    private function sendMail($email, $prefix_subject, $html)
    {
        $site_title = bwCore::getConfig()->getValue('site.titulo');
        $site_domain = $_SERVER['HTTP_HOST'];
        $site_email = sprintf('%s <mail-noreply@%s>', $site_title, $site_domain);
        $subject = $prefix_subject . ' - ' . $site_title;

        $headers = sprintf("MIME-Version: 1.0 \r\n");
        $headers .= sprintf("Content-type: text/html; charset=utf-8 \r\n");
        $headers .= sprintf("From: %s \r\n", $site_email);
        $headers .= sprintf("Reply-To: mail-noreply@%s \r\n", $site_domain);
        $headers .= sprintf("X-Mailer: PHP/%s \r\n", phpversion());

        return mail($email, $subject, $html, $headers);
    }

}
