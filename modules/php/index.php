<?php

defined('BW') or die("Acesso Restrito");

// parametros
$params = array_merge(array(
            'titulo' => 'PHP',
            'tagId' => 'mod-php-open',
            'tagClass' => 'mod-php-open',
            'file' => ''
                ), $params);


$page = bwUtil::stringURLSafe($params['file']);
$file = bwPhp::getInstance()->getPathFileMod($page);

if (bwFile::exists($file))
{
    echo "\n";
    echo '<div id="' . $params['tagId'] . '" class="' . $params['tagClass'] . '">';
    echo '<h2><span>' . $params['titulo'] . '</span></h2>';

    require($file);

    echo '</div>';
}
else
{
    bwError::show("O arquivo '{$file}' não foi encontrado!");
}
?>
