<?php

/**
 * Plugin Name:       		Shopkeeper Portfolio
 * Plugin URI:        		https://shopkeeper.wp-theme.design/
 * Description:       		Portfolio custom post type for Shopkeeper
 * Version:           		1.0
 * Author:            		GetBowtied
 * Author URI:				https://getbowtied.com
 * Text Domain:				shopkeeper-portfolio
 * Domain Path:				/languages/
 * Requires at least: 		5.0
 * Tested up to: 			5.1
 *
 * @package  Shopkeeper Portfolio
 * @author   GetBowtied
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Plugin Updater
// require 'core/updater/plugin-update-checker.php';
// $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
// 	'https://raw.githubusercontent.com/getbowtied/shopkeeper-portfolio/master/core/updater/assets/plugin.json',
// 	__FILE__,
// 	'shopkeeper-portfolio'
// );

global $theme;
$theme = wp_get_theme();
if ( $theme->template == 'shopkeeper') {
	// do stuff
}
