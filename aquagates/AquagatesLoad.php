<?php

/**
 * Load each Aquagates Payments class
 */
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/AquagatesEntity.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/AquagatesService.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/AquagatesActionGateway.php';

class AquagatesLoad{

}

/**
 * Acceptance of ajax actions executed by Aquagates Payments admin
 */
function ajax_admin_action_aquagates_payments(){
	$AquagatesService = new AquagatesActionGateway();
	$json             = $AquagatesService->Gateway_Main();
	wp_die( $json );
}

add_action( 'wp_ajax_admin_action_aquagates_payments', 'ajax_admin_action_aquagates_payments' );
