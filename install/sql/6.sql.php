-- <? defined('BW') or die("Acesso negado!"); ?>


-- 
ALTER TABLE `bw_versao` CHANGE `bw_5` `bw_6` INT(1) NOT NULL;


--
UPDATE `bw_configuracoes` SET `value` = '0' WHERE `bw_configuracoes`.`var` = 'core.site.usewww';


--
UPDATE `bw_configuracoes` SET `desc` = 'Descrição padrão do site (máx. 170 caracteres)' WHERE `bw_configuracoes`.`var` = 'core.seo.description';
