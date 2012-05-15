<?php

defined('BW') or die("Acesso negado!");

class bwButton
{

    public function getId()
    {
        return 'bt' . rand();
    }

    public function redirect($nome, $url, $class = '')
    {
        $id = bwButton::getId();

        $url = bwRouter::_($url);
        $html = '<a class="button redirect ' . $class . '" id="' . $id . '">' . $nome . '</a>';

        $html .= "<script type=\"text/javascript\">
					$('#{$id}').click(function(){
						window.location = '{$url}';
					}).button();
				</script>";

        return $html;
    }

    public function cadastrar($url)
    {
        return bwButton::redirect('Cadastrar', $url, 'cadastrar');
    }

    public function pesquisar($url, $nome = 'Pesquisar', $class = 'pesquisar')
    {
        $id = bwButton::getId();
        $id2 = bwButton::getId();

        $url = bwRouter::_($url);

        $html = '<div id="' . $id . '" class="' . $class . '"><input type="input" class="ui-corner-all" value="" />';
        $html .= '<a class="button redirect ' . $class . '" id="' . $id2 . '">' . $nome . '</a></div>';

        $html .= "<script type=\"text/javascript\">
					$('#{$id2}').click(function(){
						window.location = '{$url}?q=' + $('#{$id} input').val();
					});
				</script>";

        return $html;
    }

}

?>
