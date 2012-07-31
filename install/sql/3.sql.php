-- <? defined('BW') or die("Acesso negado!"); ?>


-- 
ALTER TABLE `bw_versao` CHANGE `bw_2` `bw_3` INT(1) NOT NULL;

--
DELETE FROM `bw_configuracoes` WHERE `bw_configuracoes`.`var` = 'core.site.pagina.inicial';
DELETE FROM `bw_configuracoes` WHERE `bw_configuracoes`.`var` = 'core.adm.template';
DELETE FROM `bw_configuracoes` WHERE `bw_configuracoes`.`var` = 'core.adm.titulo';
DELETE FROM `bw_configuracoes` WHERE `bw_configuracoes`.`var` = 'core.cache.modulos';

--
DROP TABLE bw_menus;

--
DROP TABLE bw_menus_itens