<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifier
 */

bwLoader::import('plugins.resizeimage.helper');

function smarty_modifier_resize($imagem, $params)
{
    $file = $imagem;  
    extract(array(
        'w' => NULL,
        'h' => NULL,
        'f' => 'inside',
        's' => 'any',
        'r' => false,
        'rp' => NULL,
    ));
    
    parse_str($params);
    
    return bwPluginResizeImageHelper::resize($file, $w, $h, $f, $s, $r, $rp);
} 

?>