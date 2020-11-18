<?php
/**
 * Plugin Name: pVerify
 * Plugin URI: https://wordpress.org/plugins/pverify
 * Description: This plugin integrates with our system. It will ask the user two authentication fields. Then on page load, an authentication call to our system would be performed, to get the auth token. Next, a website with the auth token as a parameter is called.
 * Version: 0.1
 * Author: pVerify
 * Author URI: https://www.pverify.com/
 * Text Domain: pverify
 * License: later
 *
 * @since 0.1
 *
 * @package pVerify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define( 'PVERIFY_PLUGIN_FILE', __FILE__ );

/**
 * Loads the action plugin
 */
require_once dirname( PVERIFY_PLUGIN_FILE ) . '/include/pVerify_Main.php';

pVerify_Main::instance();

register_activation_hook( PVERIFY_PLUGIN_FILE, array( 'pVerify_Main', 'activate' ) );

register_deactivation_hook( PVERIFY_PLUGIN_FILE, array( 'pVerify_Main', 'deactivate' ) );

register_uninstall_hook( PVERIFY_PLUGIN_FILE, array( 'pVerify_Main', 'uninstall' ) ); 
