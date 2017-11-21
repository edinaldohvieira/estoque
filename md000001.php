<?php 
function md000001_create_module() {
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  global $wpdb;
  global $charset_collate;
  $sql = "
  CREATE TABLE IF NOT EXISTS `".SCM_WPDB_PREFIX."md000001` (
  `md000001_codigo` int(11) NOT NULL AUTO_INCREMENT,
  `md000001_modulo` int(11) DEFAULT NULL,
  `md000001_grupo` int(11) DEFAULT NULL,
  `md000001_origem` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_tabela` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_campo` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_value` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_label` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_hidelabel` int(11) DEFAULT NULL,
  `md000001_show` int(11) DEFAULT '1',
  `md000001_ordem` int(11) DEFAULT NULL,
  `md000001_ctr_new` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_ctr_edit` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_ctr_view` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_ctr_list` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_ctr_loc` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_ctr_lst` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_ctr_vitrine` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_dm` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_tipo` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_height` int(11) DEFAULT NULL,
  `md000001_largura` int(11) DEFAULT NULL,
  `md000001_altura` int(11) DEFAULT NULL,
  `md000001_tamanho` int(11) DEFAULT NULL,
  `md000001_align` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_hidden` int(11) DEFAULT NULL,
  `md000001_black` int(11) DEFAULT NULL,
  `md000001_cls` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_style` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_cls_cp` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_cls_view` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_cls_vitrine` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_clslabel` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_ctcls` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_itemcls` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_formato` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_renderer` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_cmb_tp` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_cmb_source` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_cmb_codigo` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_cmb_descri` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_access_pub` int(11) DEFAULT '0',
  `md000001_access_usr` int(11) DEFAULT '0',
  `md000001_access_adm` int(11) DEFAULT '0',
  `md000001_access_root` int(11) DEFAULT '0',
  `md000001_url` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_url_md` int(11) DEFAULT NULL,
  `md000001_url_op` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_param` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_modo` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_cp_url` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_ativo` varchar(1) COLLATE utf8mb4_unicode_520_ci DEFAULT 's',
  `md000001_qtd_gr` int(11) DEFAULT '0',
  `md000001_somar` varchar(1) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_qtd_submnu` int(11) DEFAULT '0',
  `md000001_cols` int(11) DEFAULT NULL,
  `md000001_rows` int(11) DEFAULT NULL,
  `md000001_fieldcls` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_url_painel` text COLLATE utf8mb4_unicode_520_ci,
  `md000001_xtype` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_type` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `md000001_size` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`md000001_codigo`),
  UNIQUE KEY `md000001_codigo` (`md000001_codigo`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci 
  ";
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $mysqli->query($sql);
}
function md000001_delete_module() {
  global $wpdb;
  $wpdb->query( "DROP TABLE IF EXISTS ".SCM_WPDB_PREFIX."md000001");
}

register_activation_hook( SCM_PATH.'/estoque.php', 'md000001_create_module' );
register_deactivation_hook( SCM_PATH.'/estoque.php', "md000001_delete_module" );
