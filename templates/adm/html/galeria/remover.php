<?php

$id = bwRequest::getInt('id');

$r = array(
    'retorno' => true,
    'msg' => 'Imagem removida com sucesso!',
    'id' => $id
);

$img = Doctrine::getTable('PlgGaleriaImagem')->find($id);

if ($img) {
    $imagem = $img->bwImagem->remove();
    $img->delete();
}

die(json_encode($r));