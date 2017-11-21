<?php 
function md000002_create_module() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	global $charset_collate;
	$sql = "
	CREATE TABLE IF NOT EXISTS `".SCM_WPDB_PREFIX."md000002` (
	`md000002_codigo` int(11) NOT NULL AUTO_INCREMENT,
	 md000002_i00021 varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_md000002` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_md000021` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_titulo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_chamada` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_url` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_painel` text COLLATE utf8_unicode_ci,
	`md000002_url_md` int(11) DEFAULT NULL,
	`md000002_url_access` int(11) DEFAULT NULL,
	`md000002_param` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_introd` text COLLATE utf8_unicode_ci,
	`md000002_conteudo` text COLLATE utf8_unicode_ci,
	`md000002_sysmenu` int(11) DEFAULT NULL,
	`md000002_ordem` int(11) DEFAULT NULL,
	`md000002_descricao` text COLLATE utf8_unicode_ci,
	`md000002_set_visivel` int(11) DEFAULT NULL,
	`md000002_restrito` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_tabela` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_open_default` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_padrao` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_access_pub` int(11) DEFAULT NULL,
	`md000002_access_usr` int(11) DEFAULT '0',
	`md000002_access_ger` int(11) DEFAULT '0',
	`md000002_access_adm` int(11) DEFAULT '0',
	`md000002_access_root` int(11) DEFAULT '6',
	`md000002_filtrar_empresa` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_filtrar_usuario` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_filtrar_filial` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_md_list` int(11) DEFAULT NULL,
	`md000002_md_new` int(11) DEFAULT NULL,
	`md000002_md_edit` int(11) DEFAULT NULL,
	`md000002_scmMdDelete` int(11) DEFAULT NULL,
	`md000002_md_view` int(11) DEFAULT NULL,
	`md000002_open_cod` int(11) DEFAULT NULL,
	`md000002_cls` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_duplicado` varchar(1) COLLATE utf8_unicode_ci DEFAULT 'N',
	`md000002_sql_sort` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_sql_limit` int(11) DEFAULT NULL,
	`md000002_sql_dir` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_de_sistema` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_ativo` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_show_title` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_show_tbar` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_html` text COLLATE utf8_unicode_ci,
	`md000002_width` int(11) DEFAULT NULL,
	`md000002_height` int(11) DEFAULT NULL,
	`md000002_renderto` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_dir` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_open` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_mdaccessini` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_open_js` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_botoes_padroes` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_reader` text COLLATE utf8_unicode_ci,
	`md000002_footer` text COLLATE utf8_unicode_ci,
	`md000002_show_context` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_show_pagin` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_show_sum` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_show_col_title` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
	`md000002_conexao` int(11) DEFAULT NULL,
	`md000002_user` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`md000002_grupo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`md000002_retirar_acentos` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
	`md000002_caixa_alta` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
	`md000002_grupalizar` int(11) DEFAULT '0',
	`md000002_show_cp_option` int(11) NOT NULL DEFAULT '0',
	`md000002_show_tcp_option` int(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`md000002_codigo`),
	UNIQUE KEY `md000002_codigo` (`md000002_codigo`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
	";
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $mysqli->query($sql);
}

function md000002_delete_module() {
  global $wpdb;
  $wpdb->query( "DROP TABLE IF EXISTS ".SCM_WPDB_PREFIX."md000002");
}

register_activation_hook( SCM_PATH.'/estoque.php', 'md000002_create_module' );
register_deactivation_hook( SCM_PATH.'/estoque.php', "md000002_delete_module" );
