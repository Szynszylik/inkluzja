<?php
/**
 * Plugin Name:			Apollo13 Framework Extensions
 * Plugin URI:			https://apollo13themes.com/rife/free
 * Description:			Adds custom post types, shortcodes and some non theme features that we use in themes build on our Apollo13 Framework
 * Author:				Apollo13 Themes
 * Author URI:			https://apollo13themes.com/
 * License:             GPLv2 or later
 * Requires at least:	4.7.0
 * Tested up to:		4.9.6
 * Version:				1.2.1
 *
 *
 * Text Domain: a13_framework_cpt
 *
 */

//no double instances
if(defined('A13FE_BASE_DIR')){
	return;
}

define('A13FE_BASE_DIR', dirname(__FILE__).'/');

//add helpers
require_once A13FE_BASE_DIR.'functions.php';

add_action( 'init', 'a13fe_register_custom_post_types' );


//flush rules on plugin activation
register_activation_hook( __FILE__, 'a13fe_activation_flush' );


//add theme shortcodes
require_once A13FE_BASE_DIR.'shortcodes/_all.php';
//add theme widgets
require_once A13FE_BASE_DIR.'widgets/_all.php';


//if WPBakery Page Builder is active add its enhancements
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active('js_composer/js_composer.php') ) {
	require_once A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/extend.php';
}
