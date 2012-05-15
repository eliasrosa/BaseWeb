<?php

defined('BW') or die("Acesso negado!");

try {
    // Inicia
    $manager = Doctrine_Manager::getInstance();

    // Insira aqui os dados de sua conexão
    $conn = Doctrine_Manager::connection(
            'mysql://' .
            bwConfig::$db_user . ':' .
            bwConfig::$db_pass . '@' .
            bwConfig::$db_host . ':' .
            bwConfig::$db_port . '/' .
            bwConfig::$db_name, 'default');

    $manager->setAttribute(Doctrine::ATTR_QUOTE_IDENTIFIER, true);
    $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
    $manager->setAttribute(Doctrine::ATTR_EXPORT, Doctrine::EXPORT_ALL);
    $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
    $manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);
    $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

    $conn->setCollate('utf8_general_ci');
    $conn->setCharset('utf8');

    $profiler = new Doctrine_Connection_Profiler();
    $manager->setListener($profiler);

    Doctrine_Core::loadModels(BW_PATH_MODELS);
} catch (Doctrine_Manager_Exception $e) {
    print $e->getMessage();
}
