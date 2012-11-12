-- <? defined('BW') or die("Acesso negado!"); ?>


-- 
ALTER TABLE `bw_versao` CHANGE `bw_4` `bw_5` INT(1) NOT NULL;


--
UPDATE `bw_configuracoes` 
SET `value` = '%title_page% - %site_name%' 
WHERE `var` = 'core.site.titulo.formato';


--
INSERT INTO `bw_configuracoes` (`var`, `value`, `default`, `tipo`, `params`, `titulo`, `protegido`, `oculto`, `desc`) 
VALUES ('core.debug.showerrors', '1', '1', 'bool', '', 'Exibir erros', '0', '0', 'Ativa/Desativa a exibição de erros do PHP');


--
INSERT INTO `bw_configuracoes` (`var`, `value`, `default`, `tipo`, `params`, `titulo`, `protegido`, `oculto`, `desc`) 
VALUES ('core.site.usewww', '1', '1', 'bool', '', 'Subdomínio www', '0', '0', 'Quando ativado, força um redirecionamento 301 para o subdomínio \'www\'. (localhost == desligado)');


--
DELETE FROM `bw_configuracoes` 
WHERE `var` = 'core.cache.configuracoes';


--
DELETE FROM `bw_configuracoes` 
WHERE `var` = 'core.cache.url';