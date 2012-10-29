<?php

$id = bwRequest::getInt('id');
$img = Doctrine::getTable('PlgGaleriaImagem')->find($id);

if ($img) {
    echo '<form>';

    echo '<div class="imagem">';
    echo sprintf('<div class="img" style="background: url(%s) no-repeat center center;"></div>', $img->bwImagem->resize(280, 280));
    echo sprintf('<a href="%s" target="_blank">Visualizar imagem original</a>', $img->bwImagem->getUrl());
    echo '</div>';

    echo '<div class="title">';
    echo 'Título da imagem<br/>';
    echo sprintf('<input type="text" name="dados[title]" value="%s">', $img->title);
    echo '</div>';

    echo '<div class="alt">';
    echo 'Alt<br/>';
    echo sprintf('<input type="text" name="dados[alt]" value="%s">', $img->alt);
    echo '<a href="javascript:">Copiar título</a>';
    echo '</div>';

    echo '<div class="status">';
    echo 'Status<br/>';
    echo '<select name="dados[status]">';
    echo sprintf('<option value="1"%s>Ativado</option>', ($img->status == 1 ? ' selected="selected"' : ''));
    echo sprintf('<option value="0"%s>Desativado</option>', ($img->status == 0 ? ' selected="selected"' : ''));
    echo '</select>';
    echo '</div>';

    echo '<div class="position">';
    echo 'Melhor posição<br/>';
    echo sprintf('<input type="text" name="dados[position]" value="%s">', $img->position);
    echo '</div>';

    echo '<br class="clearfix" />';
    
    echo sprintf('<input type="hidden" name="dados[id]" value="%s">', $img->id);
    echo '<input type="submit" value="Salvar dados" class="submit" />';
    echo '<input type="button" value="Fechar" class="fechar" />';

    echo '</form>';
}

die();