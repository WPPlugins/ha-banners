<?php
/*
Plugin Name: HAA Banners
Description: Banners Rotations
Version: 1.1
Author: Alex Khamdamov
*/
if ( ! defined( 'ABSPATH' ) ) exit;

register_activation_hook(__FILE__, 'haa_banners_set_options');
register_deactivation_hook(__FILE__, 'haa_banners_unset_options');

wp_register_style( 'haa_banner-style', plugins_url( 'ha-banners/css/haa_banner-style.css' ) );
if (is_admin()) {
	wp_register_script( 'haa_banner-script', plugins_url( '/js/ha-banner-script.js', __FILE__ ) );
	wp_enqueue_script( 'haa_banner-script' );
} 
wp_enqueue_style( 'haa_banner-style' );

function ha_banner_ajax_load_scripts() {	
	wp_enqueue_script( "ajax-test", plugin_dir_url( __FILE__ ) . '/js/script.js', array( 'jquery' ) ); 
	wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
}
add_action('wp_print_scripts', 'ha_banner_ajax_load_scripts');

add_action('plugins_loaded', 'haa_banners_init');
function haa_banners_init() {
	load_plugin_textdomain( 'haa_banners_lang', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function haa_banners_activate() { 
	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/haa_banners';
	if (! is_dir($upload_dir)) {
	   mkdir( $upload_dir, 0700 );
	}
}
 
register_activation_hook( __FILE__, 'haa_banners_activate' );

$haa_banners_prefs_table = haa_banners_get_table_handle();
function haa_banners_get_table_handle() {
    global $wpdb;
    return $wpdb->prefix . "haa_banners_preferences";
}

function haa_banners_set_options() {
    global $wpdb;   

    $haa_banners_prefs_table = haa_banners_get_table_handle();
    $charset_collate = '';
    if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') )
            $charset_collate = "DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci";
           
    if($wpdb->get_var("SHOW TABLES LIKE '$haa_banners_prefs_table'") != $haa_banners_prefs_table) {
        $sql = "CREATE TABLE `" . $haa_banners_prefs_table . "` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(255) NOT NULL default '',			
			`name_img` VARCHAR(255) NOT NULL default '',
			`img_link` VARCHAR(255) NOT NULL default '',
			`img_target` VARCHAR(255) NOT NULL default '',
			`img_format` VARCHAR(255) NOT NULL default '',
			`img_width` VARCHAR(255) NOT NULL default '',
			`img_height` VARCHAR(255) NOT NULL default '',
			`img_clicks` VARCHAR(255) NOT NULL default '',
			`img_views` VARCHAR(255) NOT NULL default '',
            UNIQUE KEY id (id)
        )$charset_collate";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
   }
}

function haa_banners_unset_options () {
    //global $wpdb, $haa_banners_prefs_table;
    //$sql = "DROP TABLE $haa_banners_prefs_table";
    //$wpdb->query($sql);
}

function haa_banners_admin_page() {
    add_options_page('HAA Banners page', 'HAA Banners', 8, __FILE__, 'haa_banners_options_page');
}

add_action('admin_menu', 'haa_banners_admin_page');

require_once(dirname(__FILE__).'/admin/admin-settings.php');
require_once(dirname(__FILE__).'/widget/habnners-widget.php');
require_once(dirname(__FILE__).'/widget/stat.php');
?>