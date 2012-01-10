-- <? defined('BW') or die("Acesso negado!"); ?>

--
ALTER DATABASE
    DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


--
CREATE TABLE `bw_versao` (
    `bw_1` INT( 1 ) NOT NULL
) ENGINE = MYISAM ;


--
CREATE TABLE IF NOT EXISTS `bw_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


--
INSERT INTO `bw_menus` (`id`, `nome`, `alias`, `status`) VALUES
(1, 'Menu principal', 'menu-principal', 1);


--
CREATE TABLE IF NOT EXISTS `bw_menus_itens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmenu` int(11) NOT NULL,
  `idpai` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `link` varchar(255) NOT NULL,
  `params` longtext NOT NULL,
  `visible` int(11) NOT NULL,
  `ordem` int(11) NOT NULL,
  `status` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


--
INSERT INTO `bw_menus_itens` (`id`, `idmenu`, `idpai`, `titulo`, `alias`, `link`, `params`, `visible`, `ordem`, `status`) VALUES
(1, 1, 0, 'Página inicial', 'home', 'index.php?com=php&view=open', 'page=index', 1, 1, 1);


--
CREATE TABLE IF NOT EXISTS `bw_configuracoes` (
  `var` varchar(50) NOT NULL,
  `value` longtext NOT NULL,
  `default` longtext,
  `tipo` varchar(20) NOT NULL,
  `params` longtext NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `protegido` int(11) NOT NULL,
  `oculto` int(11) NOT NULL,
  `desc` longtext NOT NULL,
  UNIQUE KEY `var` (`var`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
INSERT INTO `bw_configuracoes` (`var`, `value`, `default`, `tipo`, `params`, `titulo`, `protegido`, `oculto`, `desc`) VALUES
('core.site.titulo', 'BaseWeb 2.0', 'BaseWeb', 'string', '', 'Título (nome)', 0, 0, 'Título do site.'),
('core.site.titulo.formato', '%title%', '%title%', 'string', '', 'Título (formato)', 0, 0, 'Formato do título do site.'),
('core.site.template', 'embranco', 'embranco', 'selectFolder', '{"path":"%BW_PATH_TEMPLATES%"}', 'Template', 0, 0, 'Template do site.'),
('core.site.offline.mensagem', 'Site em manutenção, por favor aguarde!', 'Site em manutenção, por favor aguarde!', 'textarea', '', 'Modo off-line (mensagem)', 0, 0, 'Mensagem de aviso, caso o site esteja offline.'),
('core.site.offline', '0', '0', 'bool', '', 'Modo off-line', 0, 0, 'Ativa/Desativa o modo offline, somente usuários cadastrados poderão visualizar o site.'),
('core.site.pagina.inicial', '1', '1', 'selectQueryDB', '{"tabela": "MenuItem", "colVal": "titulo"}', 'Página inicial', 0, 0, 'Página inicial do site.'),
('core.adm.titulo', 'Administração', 'Administração', 'string', '', 'Título', 0, 0, 'Título do sistema.'),
('core.adm.template', 'adm', 'adm', 'selectFolder', '{"path":"%BW_PATH_TEMPLATES%"}', 'Template', 0, 0, 'Template do sistema.'),
('core.adm.offline.mensagem', 'No momento o sistema está em manutenção, por favor aguarde!', 'No momento o sistema está em manutenção, por favor aguarde!', 'textarea', '', 'Modo off-line (mensagem)', 0, 0, 'Mensagem de aviso, caso o sistema esteja offline.'),
('core.adm.offline', '0', '0', 'bool', '', 'Modo off-line', 0, 0, 'Ativa/Desativa o modo offline, somente usuários do grupo de administradores poderão entrar o sistema.'),
('core.cache.url', '0', '0', 'bool', '', 'URL', 0, 0, 'Ativa/Desativa o cache das URL geradas pelo bwRouter.'),
('core.cache.configuracoes', '0', '0', 'bool', '', 'Configurações', 0, 0, 'Ativa/Desativa o cache das configurações cadastradas no banco de dados.'),
('core.cache.modulos', '0', '0', 'bool', '', 'Módulos', 0, 0, 'Ativa/Desativa o cache dos módulos do sistema.'),
('core.debug.status', '0', '0', 'bool', '', 'Status', 0, 0, 'Ativa/Desativa o debug no rodapé das páginas.'),
('plugins.analytics.code', '', '', 'string', '', 'Google Analytics', 0, 0, 'Ativa/Desativa o controle de visitas do Google Analytics.\r\nDeixe em branco para desativar.'),
('plugins.tinymce.parametros', '{\n    plugins: ''advhr,insertdatetime,preview,paste,table,imgmap'',\n    theme_advanced_buttons1: ''formatselect,styleselect,|,preview,code,|,bold,italic,underline,strikethrough,|,forecolor,backcolor,removeformat,|,justifyleft,justifycenter,justifyright,justifyfull,|,undo,redo,|,image,imgmap,|,link,unlink,|,hr,advhr'',\n    theme_advanced_buttons2: ''bullist,numlist,sub,sup,outdent,indent,|,charmap,|,pastetext,pasteword,|,tablecontrols'',\n    theme_advanced_buttons3: '''',\n    theme_advanced_toolbar_location: ''top'',\n    theme_advanced_toolbar_align: ''left'',\n    theme_advanced_statusbar_location: ''bottom'',\n    theme_advanced_resizing: false,\n    height: 400,\n    width: 85,\n    style_formats : []\n}', '', 'textarea', '', 'TinyMCE', 0, 0, 'Parâmetros JSON do plugin TinyMCE')
;


--
CREATE TABLE IF NOT EXISTS `bw_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idgrupo` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user` varchar(50) NOT NULL,
  `pass` varchar(40) NOT NULL,
  `status` int(1) NOT NULL,
  `dataLastVisit` datetime NOT NULL,
  `dataRegistro` datetime NOT NULL,
  `lastIp` varchar(15) NOT NULL,
  `lastSessionId` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


--
INSERT INTO `bw_usuarios` (`id`, `idgrupo`, `nome`, `email`, `user`, `pass`, `status`, `dataLastVisit`, `dataRegistro`, `lastIp`, `lastSessionId`) VALUES
(1, 1, 'Admin', '', 'admin', 'd310e587c0f6a77ffd420832da84710f9c4b26c9', 1, '2012-01-06 14:40:22', '2010-09-17 13:14:44', '127.0.0.1', '370efd43d4c4fff0367b1360477d1eee');


--
CREATE TABLE IF NOT EXISTS `bw_usuarios_grupos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `isAdm` int(1) NOT NULL,
  `descricao` longtext NOT NULL,
  `permissoes` longtext NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


--
INSERT INTO `bw_usuarios_grupos` (`id`, `nome`, `isAdm`, `descricao`, `permissoes`, `status`) VALUES
(1, 'Administrador', 1, '', '', 1);
