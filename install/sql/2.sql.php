-- <? defined('BW') or die("Acesso negado!"); ?>

--
ALTER TABLE `bw_versao` CHANGE `bw_1` `bw_2` INT(1) NOT NULL;

--
INSERT INTO `bw_configuracoes` (`var`, `value`, `default`, `tipo`, `params`, `titulo`, `protegido`, `oculto`, `desc`) 
VALUES ('core.seo.keywords', '', '', 'string', '', 'Palavras-chave', '0', '0', 'Palavras-chaves padrões do site');

--
INSERT INTO `bw_configuracoes` (`var`, `value`, `default`, `tipo`, `params`, `titulo`, `protegido`, `oculto`, `desc`) 
VALUES ('core.seo.description', '', '', 'textarea', '', 'Descrição', '0', '0', 'Descrição padrão do site (máx. 160 caracteres)');

