<?php
/**
 * Receive payment result notification of link payment by post and save the result
 *
 */

header( "Access-Control-Allow-Origin: *" );
$json     = file_get_contents( "php://input" );
$contents = json_decode( $json, true );
?>
<?php if ( is_array( $contents ) && count( $contents ) > 0 ){ ?>
	OK
<?php }else{ ?>
	<html>
	<head>
		<meta http-equiv="refresh" content="1;URL=/">
	</head>
	<body>
	Please wait ...
	</body>
	</html>
<?php } ?>
<?php
if ( is_array( $contents ) && count( $contents ) > 0 ){
	try{
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/AquagatesLoad.php';

		$AquagatesEntity = new AquagatesEntity();

		$result    = isset( $contents[ 'result' ] ) ? sanitize_text_field( $contents[ 'result' ] ) : "";
		$ordid     = isset( $contents[ 'ordid' ] ) ? sanitize_text_field( $contents[ 'ordid' ] ) : "";
		$order     = wc_get_order( $ordid );
		$money     = isset( $contents[ 'money' ] ) ? sanitize_text_field( $contents[ 'money' ] ) : "";
		$paymentid = isset( $contents[ 'paymentid' ] ) ? sanitize_text_field( $contents[ 'paymentid' ] ) : "";
		$usrtkn    = isset( $contents[ 'usrtkn' ] ) ? sanitize_text_field( $contents[ 'usrtkn' ] ) : "";
		$cardtkn   = isset( $contents[ 'cardtkn' ] ) ? sanitize_text_field( $contents[ 'cardtkn' ] ) : "";
		$acceptid  = isset( $contents[ 'acceptid' ] ) ? sanitize_text_field( $contents[ 'acceptid' ] ) : "";

		if ( $order !== false ){
			if ( $result == "success" ){
				$order->add_meta_data( 'aquagates-payments-link_status', 'honkessai', true );
				$order->add_meta_data( 'aquagates-payments-link_kessai_kingaku', $money, true );
				$order->add_meta_data( 'aquagates-payments-link_acceptid', $acceptid, true );
				$order->add_meta_data( 'aquagates-payments-link_cardtkn', $cardtkn, true );
				$order->add_meta_data( 'aquagates-payments-link_paymentid', $paymentid, true );
				$order->add_meta_data( 'aquagates-payments-link_paytime', $AquagatesEntity->date_YMDHIS(), true );
				$order->add_meta_data( 'aquagates-payments-link_usrtkn', $usrtkn, true );
				$order->save_meta_data();
				$order->set_transaction_id( $paymentid );

			}else if ( $result == "failure" ){
				$order->add_meta_data( 'aquagates-payments-link_status', 'mikessai', true );
				$order->add_meta_data( 'aquagates-payments-link_kessai_kingaku', 0, true );
				$order->add_meta_data( 'aquagates-payments-link_acceptid', "", true );
				$order->add_meta_data( 'aquagates-payments-link_cardtkn', "", true );
				$order->add_meta_data( 'aquagates-payments-link_paymentid', "", true );
				$order->add_meta_data( 'aquagates-payments-link_paytime', $AquagatesEntity->date_YMDHIS(), true );
				$order->add_meta_data( 'aquagates-payments-link_usrtkn', "", true );
				$order->save_meta_data();
			}
		}

		http_response_code( 200 );

	}catch( \Throwable $e ){

		http_response_code( 500 );

		$Msg = "";
		if ( !is_null( $e->getPrevious() ) ){
			$e = $e->getPrevious();
		}
		$Msg .= "[Type] : " . get_class( $e ) . "\n";
		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ){
			$Msg .= "[Code] : " . $e->getCode() . "\n";
			$Msg .= "[File] : " . $e->getFile() . "\n";
			$Msg .= "[Line] : " . $e->getLine() . "\n";
		}
		$Msg .= "[Msg] : " . $e->getMessage() . "\n";
		echo esc_html($Msg);
	}
}
