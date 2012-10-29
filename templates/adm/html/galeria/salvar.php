<?php

$dados = bwRequest::getVar('dados', array());
$db = bwComponent::save('PlgGaleriaImagem', $dados);
$r = bwComponent::retorno($db);
$r['id'] = $dados['id'];

die(json_encode($r));