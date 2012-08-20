<?
defined('BW') or die("Acesso negado!");

// ADM
bwRouter::addUrl('/usuarios');
bwRouter::addUrl('/usuarios/task', array('type' => 'task'));
bwRouter::addUrl('/usuarios/lista');
bwRouter::addUrl('/usuarios/cadastro/:id', array('fields' => array('id')));
bwRouter::addUrl('/usuarios/grupos/lista');
bwRouter::addUrl('/usuarios/grupos/cadastro/:id', array('fields' => array('id')));
