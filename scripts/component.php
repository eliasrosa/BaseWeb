<?php

require './inc/init.php';
require './inc/component.creator.php';

if (in_array('--create-all', $argv)) {
    $c = new ComponentCreator();
    $c->create();
    $c->createSql();
    $c->createModels();
    $c->createMenu();
    $c->createRouter();
    $c->createViews();
    //$c->createEvents();

    die();
}


console_log("Cria um componente simples no BW");
console_log("Modo de usar: ./component.php [opções]");
console_log();
console_log("OPÇÕES");
console_log("    --create-all   Cria um novo componente");
console_log();
console_log();

