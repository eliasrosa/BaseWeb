<?
defined('BW') or die("Acesso negado!");
$page = bwUtil::stringURLSafe($params['page']);
$file = bwPhp::getInstance()->getPathFileView($page);

if (bwFile::exists($file))
    require_once($file);
else
    bwError::show("O arquivo '{$file}' não foi encontrado!");
?>
