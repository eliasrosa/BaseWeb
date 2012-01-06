<?
defined('BW') or die("Acesso negado!");

class bwUpgrade
{
    function execute($versaoAtual)
    {
        bwCore::getConexao()->exec(bwUtil::execPHP(BW_PATH_UPGRADE_SQL .DS. $versaoAtual . '.sql.php'));
    }
}
?>
