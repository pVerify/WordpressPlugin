<?php
/**
 * Plugin Name: pVerify
 * Plugin URI: https://wordpress.org/plugins/pverify
 * Description: pVerify WP plugin to support Eligibility and Estimate Widget.
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
require_once dirname( PVERIFY_PLUGIN_FILE ) . '/includes/pVerify_Main.php';

pVerify_Main::instance();

register_activation_hook( PVERIFY_PLUGIN_FILE, array( 'pVerify_Main', 'activate' ) );

register_deactivation_hook( PVERIFY_PLUGIN_FILE, array( 'pVerify_Main', 'deactivate' ) );

register_uninstall_hook( PVERIFY_PLUGIN_FILE, array( 'pVerify_Main', 'uninstall' ) ); 
<?php
/**
 * Plugin Name: pVerify
 * Plugin URI: https://wordpress.org/plugins/pverify
 * Description: pVerify WP plugin to support Eligibility and Estimate Widget.
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
require_once dirname( PVERIFY_PLUGIN_FILE ) . '/includes/pVerify_Main.php';

pVerify_Main::instance();

register_activation_hook( PVERIFY_PLUGIN_FILE, array( 'pVerify_Main', 'activate' ) );

register_deactivation_hook( PVERIFY_PLUGIN_FILE, array( 'pVerify_Main', 'deactivate' ) );

register_uninstall_hook( PVERIFY_PLUGIN_FILE, array( 'pVerify_Main', 'uninstall' ) ); 
