<?php 
if ( ! defined( 'ABSPATH' ) ) { exit; }
function md000700_parse_request( &$wp ) {
    if(($wp->request == 'md000700') ) {
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
        echo do_shortcode('[md000700]');
        get_footer();
        exit;
  }
}
add_action( 'parse_request', 'md000700_parse_request');

function md000700_create_module() {
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  global $wpdb;
  global $charset_collate;

  $sql = "
  CREATE TABLE IF NOT EXISTS `".SCM_WPDB_PREFIX."md000700` (
    `md000700_codigo` bigint(20) NOT NULL AUTO_INCREMENT,
    `md000700_ref` varchar(20) DEFAULT NULL,
    `md000700_produto` varchar(50) NOT NULL,
    `md000700_estoque` int(11) DEFAULT '0',
    
    PRIMARY KEY (`md000700_codigo`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;
  ";
  $wpdb->query($sql);

  $sql = "
  INSERT INTO `".SCM_WPDB_PREFIX."md000002` (`md000002_codigo`, `md000002_md000002`, `md000002_i00021`, `md000002_titulo`, `md000002_chamada`, `md000002_url`, `md000002_painel`, `md000002_url_md`, `md000002_url_access`, `md000002_param`, `md000002_introd`, `md000002_conteudo`, `md000002_sysmenu`, `md000002_ordem`, `md000002_descricao`, `md000002_set_visivel`, `md000002_restrito`, `md000002_tabela`, `md000002_open_default`, `md000002_padrao`, `md000002_access_pub`, `md000002_access_usr`, `md000002_access_ger`, `md000002_access_adm`, `md000002_access_root`, `md000002_filtrar_empresa`, `md000002_filtrar_usuario`, `md000002_filtrar_filial`, `md000002_md_list`, `md000002_md_new`, `md000002_md_edit`, `md000002_scmMdDelete`, `md000002_md_view`, `md000002_open_cod`, `md000002_cls`, `md000002_duplicado`, `md000002_sql_sort`, `md000002_sql_limit`, `md000002_sql_dir`, `md000002_de_sistema`, `md000002_ativo`, `md000002_show_title`, `md000002_show_tbar`, `md000002_html`, `md000002_width`, `md000002_height`, `md000002_renderto`, `md000002_dir`, `md000002_open`, `md000002_mdaccessini`, `md000002_open_js`, `md000002_botoes_padroes`, `md000002_reader`, `md000002_footer`, `md000002_show_context`, `md000002_show_pagin`, `md000002_show_sum`, `md000002_show_col_title`, `md000002_conexao`, `md000002_user`, `md000002_grupo`, `md000002_retirar_acentos`, `md000002_caixa_alta`, `md000002_grupalizar`, `md000002_show_cp_option`, `md000002_show_tcp_option`) VALUES
  (000700, 'md000002', 'md000002', 'md000002', 'md000002', '', '', 0, 0, '', '', '', 0, 0, '', 0, '', 'md000700', '', '', 0, 0, 0, 0, 0, '', '', '', 0, 0, 0, 0, 0, 0, '', '', 'md000700_codigo', 10, 'DESC', '', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', 0, '', '', '', '', 0, 0, 0);
  ";
  $wpdb->query($sql);

  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $sql = "


    delete from ".SCM_WPDB_PREFIX."md000001 where md000001_modulo = 000700;
  

  ";
  // $mysqli->multi_query($sql);
	scm_create_fields("000700");

}

function md000700_delete_module() {
    global $wpdb;
    $wpdb->query( "delete from ".SCM_WPDB_PREFIX."md000002 where md000002_codigo = 000700;");
    $wpdb->query( "delete from ".SCM_WPDB_PREFIX."md000001 where md000001_modulo = 000700;");
    // $wpdb->query( "drop table if exists ".SCM_WPDB_PREFIX."md000700");
}

register_activation_hook( SCM_PATH.'/estoque.php', 'md000700_create_module' );
register_deactivation_hook( SCM_PATH.'/estoque.php', "md000700_delete_module" );


function md000700($atts, $content = null){
?>
        <div style="display: grid;grid-template-columns: 1fr 1fr 1fr;">
            <div class=""></div>
            <div class="">
                <div style="text-align: center;"><h4><strong>ESTOQUE</strong></h4></div>
                <div style="text-align: center;">
                	<?php echo do_shortcode('[scm_busca target="'.site_url().'/md000700/"  role="administrator,editor,author,contributor,subscriber"]') ?>
                </div>
                <div style="text-align: center;">
                    <?php echo do_shortcode('[scm_botao label="LISTAGEM | " target="?" class="btn btn-default" role="administrator,editor,author,contributor,subscriber"]') ?>
                    <?php echo do_shortcode('[scm_botao label="NOVO | " target="?op=nnew" class="btn btn-default" role="administrator,editor,author,contributor,subscriber"]') ?>
                    <?php echo do_shortcode('[scm_botao label="EDITAR | " target="?op=edit&cod=__cod__" class="btn btn-default" on_op="view" role="administrator,editor,author,contributor,subscriber"]') ?>
                    <?php echo do_shortcode('[scm_botao label="DELETAR | " target="?op=deletar&cod=__cod__" class="btn btn-default" on_op="view" role="administrator,editor,author,contributor,subscriber"]') ?>
                </div>
            </div>
            <div class=""></div>
        </div>

        <div style="height: 20px;"></div>
        <?php echo do_shortcode('[scm_list md=000700 on_op="empty" col_x0="" col_url="md000700_produto:<a href=\'?op=view&cod=__md000700_codigo__&pai=__md000700_codigo__\'>__this__</a>" un_show="" style=""  role="administrator,editor,author,contributor,subscriber"]'); ?> 

        <div style="display: grid;grid-template-columns: 1fr 1fr 1fr;">
            <div></div>
            <div>
                <?php echo do_shortcode('[scm_nnew md=000700 on_op="nnew" un_show="" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_insert md=000700 on_op="insert" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_view md=000700 cod=__cod__ on_op="view" un_show="" style="text-transform: uppercase;" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php //echo do_shortcode('[scm_text on_op="view"]<div style="text-align:center;"><br>SITUAÇÃO CADASTRAL</div>[/scm_text]'); ?>
                <?php //echo do_shortcode('[scm_view md=000305 cod=__cod__ on_op="view" un_show="i000305_codigo i000305_data i000305_hora"]'); ?>
                <?php echo do_shortcode('[scm_edit md=000700 cod=__cod__ on_op="edit" un_show="md000700_estoque" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_update md=000700 cod=__cod__ on_op="update" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_deletar md=000700 cod=__cod__ on_op="deletar" role="administrator,editor,author,contributor,subscriber"]'); ?>
                <?php echo do_shortcode('[scm_delete md=000700 cod=__cod__ on_op="delete" role="administrator,editor,author,contributor,subscriber"]'); ?>
            </div>
            <div></div>
        </div>
        <?php 
}
add_shortcode("md000700", "md000700");
add_shortcode("estoque_listagem", "md000700");
