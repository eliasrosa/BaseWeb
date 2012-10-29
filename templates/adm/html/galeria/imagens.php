<?php

function getImagem($img)
{
    return array(
        'id' => $img->id,
        'title' => $img->title,
        'alt' => $img->alt,
        'ordem' => (int) $img->ordem,
        'url' => $img->bwImagem->resize(150, 150, 'outside'),
    );
}


if (bwRequest::getInt('id_imagem')) {

    $img = Doctrine::getTable('PlgGaleriaImagem')
        ->find(bwRequest::getInt('id_imagem'));

    $r = getImagem($img);
    die(json_encode($r));
}

if (!bwRequest::getSafeVar('keysafe'))
    die('Key inválida');

$safeValue = bwRequest::getSafeVar('keysafe');
list($component, $model, $album_name, $record_id) = explode('::', $safeValue);

$imagens = Doctrine::getTable($model)
    ->findOneById($record_id)
    ->bwGaleria
    ->getImagens($album_name);

$r = array();
foreach ($imagens as $img) {
    $r[] = getImagem($img);
}

//
die(json_encode($r));