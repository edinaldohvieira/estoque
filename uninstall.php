<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
 
delete_option( 'tutsup_plugin' );
delete_site_option( 'tutsup_plugin' );