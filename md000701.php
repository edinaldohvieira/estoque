<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
function md000701_parse_request( &$wp ) {
    if(($wp->request == 'md000701') ) {
        if (!scmIsRole('administrator,editor,author,contributor,subscriber')) { 
            get_header();
            echo '<div style="text-align:center;">';
            echo '<h1>LOGIN</h1>';
            echo '<div style="padding:20px;color:red;">ESTA PÁGINA PODE ESTAR DISPONÍVEL <br> APENAS PARA USUÁRIO LOGADOS.</div>';
            echo do_shortcode('[scm_botao label="login" target="__site_url__/login/"]') ;
            //'<a class="btn btn-primary" href="/login/">LOGIN</a>'
            echo '</div>';
            get_footer();
            exit;
        }
        get_header();
        echo do_shortcode('[md000701]');
        get_footer();
        exit;
  }
}
add_action( 'parse_request', 'md000701_parse_request');

function md000701_create_module() {
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  global $wpdb;
  global $charset_collate;

  $sql = "
  CREATE TABLE IF NOT EXISTS `".SCM_WPDB_PREFIX."md000701` (
    `md000701_codigo` bigint(20) NOT NULL AUTO_INCREMENT,
    `md000701_data` date,
    `md000701_hora` varchar(10),
    `md000701_cod_produto` int,
    `md000701_quant` int(11) DEFAULT '0',
    `md000701_nota` varchar(50),
    
    PRIMARY KEY (`md000701_codigo`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
  ";
  $wpdb->query($sql);

  $sql = "
  INSERT INTO `".SCM_WPDB_PREFIX."md000002` (`md000002_codigo`, `md000002_md000002`, `md000002_i00021`, `md000002_titulo`, `md000002_chamada`, `md000002_url`, `md000002_painel`, `md000002_url_md`, `md000002_url_access`, `md000002_param`, `md000002_introd`, `md000002_conteudo`, `md000002_sysmenu`, `md000002_ordem`, `md000002_descricao`, `md000002_set_visivel`, `md000002_restrito`, `md000002_tabela`, `md000002_open_default`, `md000002_padrao`, `md000002_access_pub`, `md000002_access_usr`, `md000002_access_ger`, `md000002_access_adm`, `md000002_access_root`, `md000002_filtrar_empresa`, `md000002_filtrar_usuario`, `md000002_filtrar_filial`, `md000002_md_list`, `md000002_md_new`, `md000002_md_edit`, `md000002_scmMdDelete`, `md000002_md_view`, `md000002_open_cod`, `md000002_cls`, `md000002_duplicado`, `md000002_sql_sort`, `md000002_sql_limit`, `md000002_sql_dir`, `md000002_de_sistema`, `md000002_ativo`, `md000002_show_title`, `md000002_show_tbar`, `md000002_html`, `md000002_width`, `md000002_height`, `md000002_renderto`, `md000002_dir`, `md000002_open`, `md000002_mdaccessini`, `md000002_open_js`, `md000002_botoes_padroes`, `md000002_reader`, `md000002_footer`, `md000002_show_context`, `md000002_show_pagin`, `md000002_show_sum`, `md000002_show_col_title`, `md000002_conexao`, `md000002_user`, `md000002_grupo`, `md000002_retirar_acentos`, `md000002_caixa_alta`, `md000002_grupalizar`, `md000002_show_cp_option`, `md000002_show_tcp_option`) VALUES
  (000701, 'md000002', 'md000002', 'md000002', 'md000002', '', '', 0, 0, '', '', '', 0, 0, '', 0, '', 'md000701', '', '', 0, 0, 0, 0, 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', 'md000701_codigo', 10, 'DESC', '', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', 0, 0, 0);
  ";
  $wpdb->query($sql);

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $sql = "


    delete from ".SCM_WPDB_PREFIX."md000001 where md000001_modulo = 000701;
  

  ";
  // $mysqli->multi_query($sql);
	scm_create_fields("000701");

	
	$sql = "DROP TRIGGER IF EXISTS `".SCM_WPDB_PREFIX."md000701_bi`;";
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  
  	$sql = "
  	CREATE TRIGGER `".SCM_WPDB_PREFIX."md000701_bi` BEFORE INSERT ON `".SCM_WPDB_PREFIX."md000701` FOR EACH ROW begin
      	if new.md000701_data is null then set new.md000701_data  = (SELECT DATE(CURRENT_TIMESTAMP())); end if;
      	if new.md000701_hora is null then set new.md000701_hora  = (SELECT TIME(CURRENT_TIMESTAMP())); end if;
  	end
  	";
  	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  	$mysqli->query($sql);

	$sql = "DROP TRIGGER IF EXISTS `".SCM_WPDB_PREFIX."md000701_ai`;";
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  	$sql = "
  	CREATE TRIGGER `".SCM_WPDB_PREFIX."md000701_ai` AFTER INSERT ON `".SCM_WPDB_PREFIX."md000701` FOR EACH ROW begin
      	update ".SCM_WPDB_PREFIX."md000700 set md000700_estoque = md000700_estoque + new.md000701_quant where md000700_codigo = new.md000701_cod_produto;
  	end
  	";
  	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  	$mysqli->query($sql);

}

function md000701_delete_module() {
    global $wpdb;
    $wpdb->query( "delete from ".SCM_WPDB_PREFIX."md000002 where md000002_codigo = 000701;");
    $wpdb->query( "delete from ".SCM_WPDB_PREFIX."md000001 where md000001_modulo = 000701;");
    $wpdb->query( "drop table if exists ".SCM_WPDB_PREFIX."md000701");
}

register_activation_hook( SCM_PATH.'/estoque.php', 'md000701_create_module' );
register_deactivation_hook( SCM_PATH.'/estoque.php', "md000701_delete_module" );


function md000701($atts, $content = null){
?>
        
        
        <div style="display: grid;grid-template-columns: 1fr 1fr 1fr;">
            <div></div>
            <div>
                <?php echo do_shortcode('[scm_nnew md=000701 on_op="empty" un_show="md000701_codigo, md000701_data, md000701_hora" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_insert md=000701 on_op="insert" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_view md=000701 cod=__cod__ on_op="view" un_show="md000701_codigo" style="text-transform: uppercase;" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php //echo do_shortcode('[scm_text on_op="view"]<div style="text-align:center;"><br>SITUAÇÃO CADASTRAL</div>[/scm_text]'); ?>
                <?php //echo do_shortcode('[scm_view md=000305 cod=__cod__ on_op="view" un_show="i000305_codigo i000305_data i000305_hora"]'); ?>
                <?php echo do_shortcode('[scm_edit md=000701 cod=__cod__ on_op="edit" un_show="md000701_codigo, md000701_data, md000701_hora" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_update md=000701 cod=__cod__ on_op="update" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_deletar md=000701 cod=__cod__ on_op="deletar" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_delete md=000701 cod=__cod__ on_op="delete" role="administrator,editor,author,contributor,subscriber"]'); ?>
            </div>
            <div></div>
        </div>
        <div style="height: 20px;"></div>
        <?php echo do_shortcode('[scm_list md=000701 on_op="empty" col_x0="" col_url="md000701_produto:<a href=\'?op=view&cod=__md000701_codigo__&pai=__md000701_codigo__\'>__this__</a>" un_show="md000701_codigo" style=""  role="administrator,editor,author,contributor,subscriber"]'); ?> 

        <?php 
}
add_shortcode("md000701", "md000701");
add_shortcode("estoque_entrada", "md000701");
