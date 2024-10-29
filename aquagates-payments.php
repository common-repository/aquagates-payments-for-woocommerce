<?php

/**
 * @package           Aquagates_Payments
 * @author            First Penguin Inc.
 * @wordpress-plugin
 * Plugin Name:       AquaGates Payments for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/aquagates-payments/
 * Description:       AquaGates Payments for Wordpress WooCommerce
 * Version:           1.0.4
 * Author:            First Penguin inc.
 * Author URI:        https://www.aqua-gates.com/
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       aquagates-payments
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ){
	die;
}

if ( !defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AQUAGATES_PAYMENTS_VERSION', '1.0.4' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aquagates-payments-activator.php
 */
function activate_aquagates_payments(){
	if ( is_aquagates_payments_woocommerce_active() ){
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-aquagates-payments-activator.php';
		Aquagates_Payments_Activator::activate();
	}else{
		add_action( 'admin_notices', 'notice_err_aquagates_payments_woocommerce_not_active' );
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aquagates-payments-deactivator.php
 */
function deactivate_aquagates_payments(){
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aquagates-payments-deactivator.php';
	Aquagates_Payments_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aquagates_payments' );
register_deactivation_hook( __FILE__, 'deactivate_aquagates_payments' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aquagates-payments.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 */
function run_aquagates_payments(){
	if ( is_aquagates_payments_woocommerce_active() ){
		$plugin = new Aquagates_Payments();
		$plugin->run();
	}else{
		add_action( 'admin_notices', 'notice_err_aquagates_payments_woocommerce_not_active' );
	}
}

run_aquagates_payments();

/**
 * Check if woocommerce plugin is active
 */
function is_aquagates_payments_woocommerce_active() : bool{
	$active_plugins = (array) get_option( 'active_plugins', array() );
	$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
}

/**
 * Message to notify when woocommerce plugin is inactive
 */
function notice_err_aquagates_payments_woocommerce_not_active(){
	echo '<div class="error"><ul><li>AquaGates Payments は、WooCommerce専用プラグインです。WooCommerceを有効にしてください。</li></ul></div>';
}