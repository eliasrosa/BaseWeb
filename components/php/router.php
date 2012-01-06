<?
defined('BW') or die("Acesso negado!");

$file = bwPhp::getInstance()->getPath() . DS . 'router.php';

if (bwFile::exists($file))
{
    require $file;
}
else
{

    function phpBuildRoute(&$query)
    {
        $segments = array();
        return $segments;
    }

    function phpParseRoute($segments)
    {
        $vars = array();
        return $vars;
    }

}
?>
