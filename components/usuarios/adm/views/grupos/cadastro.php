<?

defined('BW') or die("Acesso negado!");

echo bwAdm::createHtmlSubMenu(2);

$id = bwRequest::getInt('id');
$i = bwComponent::openById('UsuarioGrupo', $id);
$login = bwLogin::getInstance()->getSession();


$form = new bwForm($i, '/usuarios/task');
$form->addH2('Informações do grupos');
$form->addInputID();
$form->addInput('nome');
$form->addTextArea('descricao');

$status = ($id != $login->Grupo->id) ? true : false;
$form->addStatus('status', array('edit' => $status));

$form->addH2('Privilégios de adminitrador');
$form->addInputRadio('isAdm', array(
    '1' => 'Sim',
    '0' => 'Não'
));

$form->addBottonSalvar('grupoSalvar');
$form->addBottonRemover('grupoRemover');
$form->show();
?>


