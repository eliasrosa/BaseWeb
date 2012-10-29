<?php

$imagens = bwRequest::getVar('imagens', array());

$r = array(
    'retorno' => true,
    'msg' => 'Ordem atualizada com sucesso'
);

foreach ($imagens as $k => $id) {
    $img = Doctrine::getTable('PlgGaleriaImagem')
        ->find($id);
    
    $img->ordem = $k;
    $img->save();
}

die(json_encode($r));