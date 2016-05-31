<?php
/**
 * Plugin Name: Breadcrumbs Path
 * Plugin URI:
 * Description: This plugin create a simple breadcrumbs path.
 * Version: 1.0.0
 * Author: Hila Moalem
 * Author URI:
 * License:
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define('PLUGINS_URL',plugins_url( '/', __FILE__ ));

include_once(dirname(__FILE__).'/admin/bcpath-shortcodes.php');

include_once(dirname(__FILE__).'/admin/bcpath-functions.php');

include_once(dirname(__FILE__).'/admin/bcpath-options.php');

/**********************************************************************************************/

// enqueue styles and scripts
add_action( 'wp_enqueue_scripts', 'my_enqueued_assets' );
function my_enqueued_assets() {
	wp_enqueue_style( 'bc-path-style', PLUGINS_URL.'css/bc-path.css' );
    wp_enqueue_style('fontawsome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css');
    wp_enqueue_style('bc-path-script', PLUGINS_URL . 'bc-path.js',array( 'jquery' ),'1.0.0',true);
}

// add options page to admin
add_action( 'admin_menu', 'bc_path_custom_admin_menu' );
function bc_path_custom_admin_menu() {
    add_options_page(
        'My Plugin Title',  // page title
        'Breadcrumbs Path', // menu title
        'manage_options', // capability
        'wporg-plugin', // menu slug
        'bc_path_options_page' // callback function
    );
}

// enqueue styles and scripts for admin plugin
add_action( 'admin_enqueue_scripts', 'admin_print_scripts' );
function admin_print_scripts($hook) {
    if ( 'settings_page_wporg-plugin' != $hook ) {
        return;
    }
    wp_enqueue_script( 'my_custom_script', PLUGINS_URL . '/js/admin-scripts.js' );

}
