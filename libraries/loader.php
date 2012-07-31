<?php
defined('BW') or die("Acesso negado!");

// loader
require(BW_PATH_LIBRARIES .DS. 'core' .DS. 'loader.php');

// auto load
bwLoader::import('doctrine.doctrine');

// inicia o auto load
spl_autoload_register('bwLoader::autoload');

// arquivos importantes
bwLoader::import('core.functions');

// carrega conexão com banco MySql
bwLoader::import('core.conexao');
