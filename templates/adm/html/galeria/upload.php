<?php

$r = array(
    'retorno' => true,
    'msg' => ''
);

if (!bwRequest::getSafeVar('keysafe')) {
    $r['retorno'] = false;
    $r['msg'] = "Key inválida";
    die(json_encode($r));
}

$safeValue = bwRequest::getSafeVar('keysafe');
list($component, $model, $album_name, $record_id) = explode('::', $safeValue);
$album_key = sprintf('%s::%s::%s', $component, $model, $album_name);


// busca o album
$album = Doctrine_Query::create()
    ->from('PlgGaleriaAlbum a')
    ->Where('a.key = ?', $album_key)
    ->fetchOne();


// caso não exista, é criado
if (!$album) {
    $album = new PlgGaleriaAlbum();
    $album->key = $album_key;
    $album->save();
}


//
$imagem = new PlgGaleriaImagem();
$imagem->album_id = $album->id;
$imagem->record_id = $record_id;
$imagem->status = 1;
$imagem->save();

//
$u = new bwImagem($component, 'galeria-'.$album_name, $imagem->id);
$u->setPath();

//apaga o arquivo anterior e o cache
$u->remove();

$file = $_FILES['user_file'];


if ($file['error'][0] == 1) {
    $r['retorno'] = false;
    $r['msg'] .= "- O tamanho inválido, arquivo muito grande<br>";
} else {

    if (bwFile::getExt($u->getPath()) != 'jpg') {
        $r['retorno'] = false;
        $r['msg'] .= "- Somente imagens .JPG<br/>";
    }

    if (!$file['size'][0]) {
        $r['retorno'] = false;
        $r['msg'] .= "- Arquivo inválido (0 bytes)<br>";
    }

    bwLoader::import('wideimage.WideImage');
    $img = WideImage::load($file['tmp_name'][0]);
    if ($img->getWidth() > 2000 || $img->getHeight() > 2000) {
        $r['retorno'] = false;
        $r['msg'] .= "- Resolução da imagem é muito grande<br/>";
    }
}

//
if ($r['retorno'] == false) {
    $imagem->delete();
    die(json_encode($r));
}

// cria a pasta
if (!bwFolder::is($u->getFolderPath())) {
    bwFolder::create($u->getFolderPath());
}

//
if (move_uploaded_file($file['tmp_name'][0], $u->getPath())) {
    $r['retorno'] = true;
    $r['msg'] = "Imagem enviada com sucesso";
    $r['id'] = $imagem->id;
} else {
    $r['retorno'] = false;
    $r['msg'] .= "- Não foi possível enviar a imagem<br/>";
}
//

if ($r['retorno'] == false) {
    $imagem->delete();
}

//
die(json_encode($r));