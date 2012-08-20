<?

defined('BW') or die("Acesso negado!");

echo bwAdm::createHtmlSubMenu(1);

$id = bwRequest::getInt('id');
$i = bwComponent::openById('Usuario', $id);
$login = bwLogin::getInstance()->getSession();


$form = new bwForm($i, '/usuarios/task');
$form->addH2('Informações do usuário');
$form->addInputID();
$form->addInput('nome');

$status = ($id != $login->id) ? true : false;
$form->addStatus('status', array('edit' => $status));

$form->addH2('Informações do grupo');
$form->addSelectDB('idgrupo', 'UsuarioGrupo');

$form->addH2('Informações de login');
$form->addInput('email');

if($i->id)
{
    $form->addH2('Informações extras');
    $form->addInput('dataRegistro', 'text', array('edit' => false));
    $form->addInput('dataLastVisit', 'text', array('edit' => false));
    $form->addInput('lastIp', 'text', array('edit' => false));
}

$form->addH2('Cadastrar senha');
$form->addInputsPassword('pass');

$form->addBottonSalvar('usuarioSalvar');


if($id != $login->id)
    $form->addBottonRemover('usuarioRemover');

$form->show();
?>


