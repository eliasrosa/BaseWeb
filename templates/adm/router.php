<?
defined('BW') or die("Acesso negado!");

// configurações
bwRouter::addUrl('/config');
bwRouter::addUrl('/config/save', array(), 'task');
bwRouter::addUrl('/login', array(), 'static');
bwRouter::addUrl('/sair', array(), 'static');
