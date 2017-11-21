<?php
/**
 * Plugin Name:     Estoque
 * Plugin URI:      https://edinaldohvieira.com/plugins/estoque
 * Description:     Controle de estoque simples com entrada e saida de produtos.
 * Author:          Edinaldo H Vieira
 * Author URI:      https://edinaldohvieira.com
 * Text Domain:     estoque
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Estoque
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define('SCM_PATH',  plugin_dir_path( __FILE__ ) );
define('SCM_WPDB_PREFIX', $GLOBALS['wpdb']->prefix );
include('functions.php');
include('md000001.php');
include('md000002.php');
include('md000700.php');
include('md000701.php');
include('md000702.php');
	
// add_filter('wp_nav_menu_items','add_search_box_to_menu', 10, 2);
function add_search_box_to_menu( $items, $args ) {
    if( $args->theme_location == 'primary' )
        return $items."<li class='menu-header-search'><form action='http://exemplo.com/' id='searchform' method='get'><input type='text' name='s' id='s' placeholder='Buscar'></form></li>";
    return $items;
}
