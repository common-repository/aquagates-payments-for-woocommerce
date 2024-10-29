<?php
/**
 * AquaGates Payments Gateway
 * Core features of link payment
 */
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/AquagatesLoad.php';

class AquagatesPayments_Link extends WC_Payment_Gateway{

	/**
	 * Constructor for the gateway.
	 */
	public function __construct(){

		$this->id         = 'aquagates_payments_link';
		$this->has_fields = true;

		$this->init_form_fields();
		$this->init_settings();
		$this->method_title       = 'AquaGates Payments リンク型決済';
		$this->method_description = 'AquaGates Payments リンク型決済を利用可能にする場合は有効にしてください';
		$this->supports           = array(
			'products',
		);

		foreach ( $this->settings as $key => $val ){
			$this->{$key} = $val;
		}

		// Actions
		add_action( 'woocommerce_update_options_payment_gateways', array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		if ( $this->enabled == "yes" ){
			add_action( 'woocommerce_before_thankyou', array( $this, 'aquagates_payments_link_before_thankyou' ) );

			add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'aquagates_payments_link_admin_order_data_after_order_details' ) );
		}
	}

	/**
	 * Initialize Gateway Settings Form Fields.
	 */
	function init_form_fields(){
		$this->form_fields = array(
			'title'                       => array(
				'title'       => 'タイトル',
				'type'        => 'text',
				'description' => '',
				'default'     => 'リンク決済'
			),
			'description'                 => array(
				'title'       => '説明',
				'type'        => 'textarea',
				'description' => '',
				'default'     => '購入完了画面に表示されるリンクから決済をおこなってください',
			),
			'order_button_text'           => array(
				'title'       => 'オーダーボタンのテキスト',
				'type'        => 'text',
				'description' => '',
				'default'     => '購入する',
			),
			'sokuji_saikessai_active_flg' => array(
				'title'       => '同額追加決済の有無',
				'type'        => 'checkbox',
				'label'       => '決済後、同額追加決済の操作を可能にする',
				'description' => '',
				'default'     => 'no',
			),
		);
	}

	/**
	 * UI - Payment page fields
	 */
	public function payment_fields(){

		echo '<div id="aquagates-payments-link-payment-fields">';

		echo '<fieldset id="wc-aquagates_payments_link-cc-form" class="wc-credit-card-form wc-payment-form">';
		if ( $this->description ){
			echo '<p class="description">' . esc_html( $this->description ) . '</p>';
		}

		echo '<div class="clear"></div>';
		echo '</fieldset>';
		echo '</div>';
	}

	/**
	 * Process the payment and return the result.
	 */
	public function process_payment( $order_id ){

		$order = wc_get_order( $order_id );

		$AquagatesEntity = new AquagatesEntity( $this );

		$order->add_meta_data( 'aquagates-payments-link_status', 'mikessai', true );
		$order->add_meta_data( 'aquagates-payments-link_kessai_kingaku', 0, true );
		$order->add_meta_data( 'aquagates-payments-link_acceptid', "", true );
		$order->add_meta_data( 'aquagates-payments-link_cardtkn', "", true );
		$order->add_meta_data( 'aquagates-payments-link_paymentid', "", true );
		$order->add_meta_data( 'aquagates-payments-link_paytime', $AquagatesEntity->date_YMDHIS(), true );
		$order->add_meta_data( 'aquagates-payments-link_usrtkn', "", true );
		$order->save_meta_data();

		wc_reduce_stock_levels( $order_id );
		WC()->cart->empty_cart();

		$Results = [ 'result' => 'success', 'redirect' => $order->get_checkout_order_received_url() ];

		return $Results;
	}

	/**
	 * Display the link payment transition button on the thank you screen
	 */
	public function aquagates_payments_link_before_thankyou( $order_id ){

		$order = wc_get_order( $order_id );
		if ( $order->get_payment_method() == $this->id ){
			$status = $order->get_meta( 'aquagates-payments-link_status' );

			$AquagatesEntity                = new AquagatesEntity( $this );
			$AquagatesEntity->WcOrderId     = $order_id;
			$AquagatesEntity->CustomerEmail = $AquagatesEntity->getCleanCustomerEmail( $order->get_billing_email() );
			$AquagatesEntity->CustomerTel   = $AquagatesEntity->getCleanCustomerTel( $order->get_billing_phone() );
			$AquagatesEntity->KessaiKingaku = $AquagatesEntity->getCleanKessaiKingaku( $order->get_total() );
			$LinkPayResultHookURL           = $AquagatesEntity->getLinkPayResultHookURL();

			if ( $status == "mikessai" ){
				echo '<p id="idAPLILinkKessaiFormText">決済は完了していません。下記のリンクより決済をおこなってください。</p>';
				echo '<form id="idAPLILinkKessaiForm" method="post" action="https://credit.aqua-gates.com/settlement/connect" >';
				echo '	<input type="hidden" id="aquagates_payments_link-clientid" name="clientid" value="' . esc_attr( $AquagatesEntity->ConfigClientId ) . '" />';
				echo '	<input type="hidden" id="aquagates_payments_link-acstkn" name="acstkn" value="' . esc_attr( $AquagatesEntity->ConfigAcstkn ) . '" />';
				echo '	<input type="hidden" id="aquagates_payments_link-type" name="type" value="payment" />';
				echo '	<input type="hidden" id="aquagates_payments_link-money" name="money" value="' . esc_attr( $AquagatesEntity->KessaiKingaku ) . '" />';
				echo '	<input type="hidden" id="aquagates_payments_link-email" name="ordid" value="' . esc_attr( $order_id ) . '" />';
				echo '	<input type="hidden" id="aquagates_payments_link-email" name="email" value="' . esc_attr( $AquagatesEntity->CustomerEmail ) . '" />';
				echo '	<input type="hidden" id="aquagates_payments_link-telno" name="telno" value="' . esc_attr( $AquagatesEntity->CustomerTel ) . '" />';
				echo '	<input type="hidden" id="aquagates_payments_link-usrid" name="usrid" value="' . esc_attr( $AquagatesEntity->ConfigUserId ) . '" />';
				echo '	<input type="hidden" id="aquagates_payments_link-successurl" name="successurl" value="' . esc_attr( $LinkPayResultHookURL ) . '" />';
				echo '	<input type="hidden" id="aquagates_payments_link-failureurl" name="failureurl" value="' . esc_attr( $LinkPayResultHookURL ) . '" />';
				echo '	<button type="submit" id="idAPLILinkKessaiButton" class="button alt" >決済画面を開いて、決済する</button>';
				echo '</form>';
			}
		}
	}

	/**
	 * Display of each button necessary for payment on the management screen
	 */
	public function aquagates_payments_link_admin_order_data_after_order_details( $order ){

		if ( $order->get_payment_method() == $this->id ){
			$AquagatesEntity = new AquagatesEntity( $this );
			$status          = $order->get_meta( 'aquagates-payments-link_status' );
			$status_label    = $AquagatesEntity->getPaymentsStatusLabel( $status );
			$kessaikingaku   = $order->get_meta( 'aquagates-payments-link_kessai_kingaku' );
			$kessaikingaku   = $AquagatesEntity->money_Format( $kessaikingaku );
			$paymentid       = $order->get_meta( 'aquagates-payments-link_paymentid' );
			$paytime         = $order->get_meta( 'aquagates-payments-link_paytime' );
			$paytime         = $AquagatesEntity->date_Format( $paytime, "Y-m-d H:i:s" );

			$button_henkincancel_disabled    = ( $AquagatesEntity->getButtonHenkinCancelActiveFlg( $status ) ) ? "" : "disabled";
			$button_sokujisaikessai_disabled = ( $AquagatesEntity->getButtonDougakuSaikessaiActiveFlg( $status ) ) ? "" : "disabled";

			$ajaxUrl     = admin_url( 'admin-ajax.php', __FILE__ );
			$nowdatetime = $AquagatesEntity->date_YMDHIS( "", "YmdHis" );


			echo '<p class="form-field form-field-wide">';
			echo '	<label>AquaGates Payments ' . esc_html( $this->title ) . ' </label>';
			echo '	<ul class="wc-order-aquagates-payments-info">';
			echo '		<li><span class="title">ステータス<span class="text">' . esc_html( $status_label ) . '</span></span></li>';
			echo '		<li><span class="title">決済済み金額<span class="text">' . esc_html( $kessaikingaku ) . '</span></span></li>';
			echo '		<li><span class="title">決済ID<span class="text">' . esc_html( $paymentid ) . '</span></span></li>';
			echo '		<li><span class="title">更新日<span class="text">' . esc_html( $paytime ) . '</span></span></li>';
			echo '		<li><button type="button" class="button henkincancel" ' . esc_attr( $button_henkincancel_disabled ) . '>返金・キャンセル</button></li>';
			echo '		<li><button type="button" class="button sokujisaikessai" ' . esc_attr( $button_sokujisaikessai_disabled ) . '>同額追加決済</button></li>';
			echo '	</ul>';
			echo '</p>';

			echo '
				<script>
					jQuery( document ).ready( function(){
						jQuery( "#woocommerce-order-data button.henkincancel").on( "click", function(){
							AquagatesPaymentsWCOrderLinkAction("henkincancel","返金・キャンセル");
						} );
						jQuery( "#woocommerce-order-data button.sokujisaikessai").on( "click", function(){
							AquagatesPaymentsWCOrderLinkAction("sokujisaikessai","同額追加決済");
						} );
					} );
					function AquagatesPaymentsWCOrderLinkAction( type, label ){
						if( confirm( label + "処理を本当に実行しますか？" ) ){
							WCOrderDataBlock();
							let order_id = "' . esc_js( $order->get_id() ) . '";
							let ajaxUrl = "' . esc_js( esc_url( $ajaxUrl ) ) . '";
							jQuery.ajax({
								url: ajaxUrl,
								type: "POST",
								// dataType: "json",
								data: {
									action : "admin_action_aquagates_payments",
									type : "creditcard_" + type,
									nowdatetime : "' . esc_js( $nowdatetime ) . '",
									order_id : order_id
								}
							}).done( function (data) {
								let val = JSON.parse(data);
								alert( val.Msg );
								if( val.ResultStatus === "success" ){
									alert( "画面を最新の状態にするため、更新をおこないます。" );
									location.reload();
								}else{
									alert( "エラーが発生しました。画面を最新の状態にして再度、お試しください。" );
									WCOrderDataUnblock();
								}
							}).fail(function( XMLHttpRequest, textStatus, error ){
							    alert( "エラーが発生しました。画面を最新の状態にして再度、お試しください。" );
							    WCOrderDataUnblock();
							});
						}
					}
					function WCOrderDataBlock(){
						jQuery( "#woocommerce-order-data" ).block({
							message: null,
							overlayCSS: {
								background: "#fff",
								opacity: 0.6
							}
						});
					}
					function WCOrderDataUnblock(){
						jQuery( "#woocommerce-order-data" ).unblock();
					}
				</script>
			';
		}
	}

}

/**
 * Add the gateway to woocommerce
 */
function woocommerce_payment_gateways_aquagates_payments_link( $methods ){
	$methods[] = 'AquagatesPayments_Link';
	return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'woocommerce_payment_gateways_aquagates_payments_link' );

/**
 * Edit the available gateway to woocommerce
 */
function woocommerce_available_payment_gateways_aquagates_payments_link( $methods ){
	$currency = get_woocommerce_currency();
	if ( $currency != 'JPY' ){
		unset( $methods[ 'aquagates_payments_link' ] );
	}
	return $methods;
}

add_filter( 'woocommerce_available_payment_gateways', 'woocommerce_available_payment_gateways_aquagates_payments_link' );

/**
 * Add Aquagates Payments Link Result Hook
 */
function add_aquagates_payments_link_payresulthook( $vars ){
	$vars[] = "aquagates-payments-link-payresulthook";
	return $vars;
}

add_filter( 'query_vars', 'add_aquagates_payments_link_payresulthook' );

/**
 * Run Aquagates Payments Link Result Hook
 */
function run_aquagates_payments_link_payresulthook( $template ){
	if ( get_query_var( 'aquagates-payments-link-payresulthook' ) == 'yes' ){
		$path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/aquagates-payments-link-payresulthook.php';
		return $path;
	}
	return $template;
}

add_filter( 'template_include', 'run_aquagates_payments_link_payresulthook' );