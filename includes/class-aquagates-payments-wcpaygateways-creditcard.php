<?php
/**
 * AquaGates Payments Gateway
 * Core functions of credit card payment
 */
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/AquagatesLoad.php';

class AquagatesPayments_Creditcard extends WC_Payment_Gateway{

	/**
	 * Constructor for the gateway.
	 */
	public function __construct(){

		$this->id         = 'aquagates_payments_creditcard';
		$this->has_fields = true;

		$this->init_form_fields();
		$this->init_settings();
		$this->method_title       = 'AquaGates Payments トークン型決済';
		$this->method_description = 'AquaGates Payments トークン型決済を利用可能にする場合は有効にしてください';
		$this->supports           = array(
			'products',
			'tokenization',
			'refunds',
			'default_credit_card_form'
		);

		foreach ( $this->settings as $key => $val ){
			$this->{$key} = $val;
		}

		// Actions
		add_action( 'woocommerce_update_options_payment_gateways', array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		if ( $this->enabled == "yes" ){
			add_filter( 'woocommerce_order_button_html', array( $this, 'aquagates_payments_creditcard_order_button_html' ) );
			add_action( 'woocommerce_checkout_after_order_review', array( $this, 'aquagates_payments_creditcard_checkout_after_order_review' ) );
		}
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'aquagates_payments_creditcard_admin_order_data_after_order_details' ) );
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
				'default'     => 'クレジットカード決済'
			),
			'description'                 => array(
				'title'       => '説明',
				'type'        => 'textarea',
				'description' => '',
				'default'     => '決済に必要な下記の項目をご入力のうえご注文ください',
			),
			'order_button_text'           => array(
				'title'       => 'オーダーボタンのテキスト',
				'type'        => 'text',
				'description' => '',
				'default'     => '購入する',
			),
			'kessai_type'                 => array(
				'title'       => '注文完了時の決済タイプ',
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'description' => '',
				'default'     => 'sokujikessai',
				'desc_tip'    => true,
				'options'     => array(
					'sokujikessai' => '即時決済',
					'yoshinnomi'   => '仮売り決済'
				)
			),
			'sokuji_saikessai_active_flg' => array(
				'title'       => '同額追加決済の有無',
				'type'        => 'checkbox',
				'label'       => '決済後、同額追加決済の操作を可能にする',
				'description' => '',
				'default'     => 'no',
			),

			/**
			 * Will be implemented in next version
			 */
			//			'shiharai_houhou_type' => array(
			//				'title'       => 'お支払い方法（複数選択可）',
			//				'type'        => 'multiselect',
			//				'class'       => 'wc-multi-select',
			//				'description' => '1回払いの他に、受付を許可するお支払い方法を選択してください',
			//				'default'     => '',
			//				'options'     => array(
			//					'bunkatsu' => '分割払い',
			//					'bonus'    => 'ボーナス1回払い',
			//					'rebo'     => 'リボ払い',
			//				)
			//			),
			//			'bunkatsu_type'        => array(
			//				'title'       => '分割払いの回数（複数選択可）',
			//				'type'        => 'multiselect',
			//				'class'       => 'wc-multi-select',
			//				'description' => '分割払いの場合、受付を許可する分割回数を選択してください',
			//				'default'     => 'ikkai',
			//				'options'     => array(
			//					'2'  => '分割 2回払い',
			//					'3'  => '分割 3回払い',
			//					'5'  => '分割 5回払い',
			//					'6'  => '分割 6回払い',
			//					'10' => '分割 10回払い',
			//					'12' => '分割 12回払い',
			//					'15' => '分割 15回払い',
			//					'18' => '分割 18回払い',
			//					'20' => '分割 20回払い',
			//					'24' => '分割 24回払い',
			//					'30' => '分割 30回払い',
			//					'36' => '分割 36回払い',
			//				)
			//			),
		);
	}

	/**
	 * UI - Payment page fields
	 */
	public function payment_fields(){

		echo '<div id="aquagates-payments-creditcard-payment-fields">';

		wp_enqueue_script( 'aquagates-payments-credit-token_generate', 'https://credit.aqua-gates.com/api/js/token_generate.js' );
		wp_enqueue_script( 'wc-credit-card-form' );

		echo '<fieldset id="wc-aquagates_payments_creditcard-cc-form" class="wc-credit-card-form wc-payment-form">';
		if ( $this->description ){
			echo '<p class="description">' . esc_html( $this->description ) . '</p>';
		}
		if ( $this->get_recommend_browser_flg() === false ){
			echo '<div id="aquagates_payments_creditcard-browser-errs">';
			echo '	<ul class="woocommerce-error">';
			echo '		<li>ご利用中のブラウザは非推奨です。正しく決済・購入ができない場合は「Chrome」にてお試しください。</li>';
			echo '	</ul>';
			echo '</div>';
		}

		echo '<div id="aquagates_payments_creditcard-validate-errs" style="display: none;">';
		echo '	<ul class="woocommerce-error">';
		echo '	</ul>';
		echo '</div>';

		echo '<p class="form-row form-row-wide">';
		echo '	<label for="aquagates_payments_creditcard-card-number">カード番号&nbsp;<span class="required">*</span></label>';
		echo '	<input id="aquagates_payments_creditcard-card-number"
					class="input-text wc-credit-card-form-card-number"
					inputmode="numeric"
					autocomplete="cc-number"
					type="tel"
					placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;"
					name="aquagates_payments_creditcard-card-number"
				/>';
		echo '</p>';
		echo '<p class="form-row form-row-first">';
		echo '	<label for="aquagates_payments_creditcard-card-expiry">有効期限(月/年)&nbsp;<span class="required">*</span></label>';
		echo '	<input id="aquagates_payments_creditcard-card-expiry"
					class="input-text wc-credit-card-form-card-expiry"
					inputmode="numeric"
					autocomplete="cc-exp"
					type="tel"
					placeholder="月 / 年"
					name="aquagates_payments_creditcard-card-expiry"
				/>';
		echo '</p>';
		echo '<p class="form-row form-row-last">';
		echo '	<label for="aquagates_payments_creditcard-card-cvc">セキュリティコード&nbsp;<span class="required">*</span></label>';
		echo '	<input id="aquagates_payments_creditcard-card-cvc"
					class="input-text wc-credit-card-form-card-cvc"
					inputmode="numeric"
					autocomplete="off"
					type="tel"
					maxlength="4"
					placeholder="CVC"
					name="aquagates_payments_creditcard-card-cvc"
				 />';
		echo '</p>';
		echo '<div class="clear"></div>';
		echo '<p class="form-row form-row-wide">';
		echo '	<label for="aquagates_payments_creditcard-card-username">カード名義&nbsp;<span class="required">*</span></label>';
		echo '	<input id="aquagates_payments_creditcard-card-username"
					class="input-text wc-credit-card-form-card-username"
					inputmode="text"
					autocomplete="cc-name"
					type="text"
					placeholder="HANAKO SATO"
					name="aquagates_payments_creditcard-card-username"
				 />';
		echo '</p>';

		if ( isset( $this->shiharai_houhou_type ) && is_array( $this->shiharai_houhou_type ) ){
			$options = $this->form_fields[ "shiharai_houhou_type" ][ "options" ];
			echo '<p class="form-row form-row-wide">';
			echo '	<label for="aquagates_payments_creditcard-card-shiharai-houhou-type">お支払い方法&nbsp;<span class="required">*</span></label>';
			echo '	<select id="aquagates_payments_creditcard-card-shiharai-houhou-type" 
						class="wc-credit-card-form-card-shiharai-houhou-type" 
						name="aquagates_payments_creditcard-card-shiharai-houhou-type"
					 />';
			echo '<option value="ikkai">1回払い</option>';
			if ( array_search( 'bonus', $this->shiharai_houhou_type ) !== false ){
				echo '<option value="bonus">' . esc_html( $options[ 'bonus' ] ) . '</option>';
			}
			if ( array_search( 'rebo', $this->shiharai_houhou_type ) !== false ){
				echo '<option value="rebo">' . esc_html( $options[ 'rebo' ] ) . '</option>';
			}
			if ( isset( $this->bunkatsu_type ) && is_array( $this->bunkatsu_type ) ){
				$options = $this->form_fields[ "bunkatsu_type" ][ "options" ];
				if ( array_search( 'bunkatsu', $this->shiharai_houhou_type ) !== false ){
					foreach ( $this->bunkatsu_type as $val ){
						echo '<option value="' . esc_attr( $val ) . '">' . esc_html( $options[ $val ] ) . '</option>';
					}
				}
			}
			echo '	</select>';
			echo '</p>';
		}

		echo '
			<script>
				jQuery("#aquagates_payments_creditcard-card-number,#aquagates_payments_creditcard-card-expiry,#aquagates_payments_creditcard-card-cvc,#aquagates_payments_creditcard-card-username,#aquagates_payments_creditcard-card-shiharai-houhou-type").
					on("blur",function() {
					APCC_FormValidateFlg = true;
					APCC_ValidateErrShow = false;
					APCC_TokenHakkou();
					APCC_SubmitButtonControll();
					return APCC_FormValidateFlg;
				});
				jQuery("#aquagates_payments_creditcard-card-number,#aquagates_payments_creditcard-card-expiry,#aquagates_payments_creditcard-card-cvc,#aquagates_payments_creditcard-card-username,#aquagates_payments_creditcard-card-shiharai-houhou-type").
					on("change",function() {
					APCC_FormValidateFlg = true;
					APCC_ValidateErrShow = false;
					APCC_TokenClear();
					APCC_SubmitButtonControll();
					return APCC_FormValidateFlg;
				});
			</script>
		';

		echo '<div class="clear"></div>';
		echo '</fieldset>';
		echo '</div>';
	}

	/**
	 * Recommended browser check
	 */
	private function get_recommend_browser_flg(){

		$HttpUserAgent       = sanitize_text_field( $_SERVER[ 'HTTP_USER_AGENT' ] );
		$RecommendBrowserFlg = true;
		if ( !isset( $HttpUserAgent ) || $HttpUserAgent == "" ){
			$RecommendBrowserFlg = false;
		}else if ( str_contains( $HttpUserAgent, 'Windows NT' ) && str_contains( $HttpUserAgent, 'rv:11.0' ) ){
			$RecommendBrowserFlg = false;
		}else if ( str_contains( $HttpUserAgent, 'Android' ) && str_contains( $HttpUserAgent, 'Linux; U;' ) && !str_contains( $HttpUserAgent, 'Chrome' ) ){
			$RecommendBrowserFlg = false;
		}else if ( str_contains( $HttpUserAgent, 'Android' ) && str_contains( $HttpUserAgent, 'Chrome' ) && str_contains( $HttpUserAgent, 'SamsungBrowser' ) ){
			$RecommendBrowserFlg = false;
		}
		return $RecommendBrowserFlg;
	}

	/**
	 * Loading JS required for screen control
	 */
	public function aquagates_payments_creditcard_order_button_html( $html ){

		$html .= '
			<script>
				jQuery( document ).ready( function(){
					APCC_SubmitButtonControll();
					APCC_InputCardsClear();
				} );
				jQuery( "input[name=payment_method]").on( "change", function(){
					APCC_SubmitButtonControll();
				} );
				jQuery("#place_order").on("click",function() {
					APCC_FormValidateFlg = true;
					APCC_ValidateErrShow = true;
					APCC_TokenHakkou();
					APCC_SubmitButtonControll();
					if(APCC_FormValidateFlg){
						APCC_InputCardsClear();
					}else{
						alert( "入力項目を確認してください。" );
					}
					return APCC_FormValidateFlg;
				});
			</script>
		';

		return $html;
	}

	/**
	 * Token issuance and validation on view
	 */
	public function aquagates_payments_creditcard_checkout_after_order_review(){

		$AquagatesEntity = new AquagatesEntity( $this );

		echo '<input type="hidden" id="aquagates_payments_creditcard-clientid" name="aquagates_payments_creditcard-clientid" value="' . esc_attr( $AquagatesEntity->ConfigClientId ) . '" />';
		echo '<input type="hidden" id="aquagates_payments_creditcard-acstkn" name="aquagates_payments_creditcard-acstkn" value="' . esc_attr( $AquagatesEntity->ConfigAcstkn ) . '" />';
		echo '<input type="hidden" id="aquagates_payments_creditcard-userid" name="aquagates_payments_creditcard-userid" value="' . esc_attr( $AquagatesEntity->ConfigUserId ) . '" />';
		echo '<input type="hidden" id="aquagates_payments_creditcard-onetimetoken" name="aquagates_payments_creditcard-onetimetoken" value="" />';
		echo '<input type="hidden" id="aquagates_payments_creditcard-onetimetokenkey" name="aquagates_payments_creditcard-onetimetokenkey" value="" />';
		echo '<input type="hidden" id="aquagates_payments_creditcard-errcode" name="aquagates_payments_creditcard-errcode" value="" />';

		echo '
			<script>
				var APCC_FormValidateFlg = true;
				var APCC_ValidateErrShow = false;
				
				function APCC_SubmitButtonControll(){
					let method = jQuery( "input[name=payment_method]:checked" ).val();
					if( method === "aquagates_payments_creditcard"){
						let token = jQuery( "#aquagates_payments_creditcard-onetimetoken" ).val();
						let tokenkey = jQuery( "#aquagates_payments_creditcard-onetimetokenkey" ).val();
						if( token !== "" && tokenkey !== "" && APCC_FormValidateFlg){
							jQuery("#place_order").prop("disabled", false);
						}else{
							jQuery("#place_order").prop("disabled", true);
						}
					}else{
						jQuery("#place_order").prop("disabled", false);
					}
				}
				
				function APCC_TokenHakkou(){
				
					APCC_Validate_Init();
					
					let token = jQuery( "#aquagates_payments_creditcard-onetimetoken" ).val();
					let tokenkey = jQuery( "#aquagates_payments_creditcard-onetimetokenkey" ).val();
					if( token === "" || tokenkey === "" ){
								
						APCC_Validate_Blank_Text( "#aquagates_payments_creditcard-card-number", "カード番号" );
						APCC_Validate_Blank_Text( "#aquagates_payments_creditcard-card-expiry", "有効期限(月/年)" );
						APCC_Validate_Blank_Text( "#aquagates_payments_creditcard-card-cvc", "セキュリティコード" );
						APCC_Validate_Blank_Text( "#aquagates_payments_creditcard-card-username", "カード名義" );
				
						if( APCC_FormValidateFlg ){
							aquagates_system.generateToken({
								clientid: jQuery( "#aquagates_payments_creditcard-clientid" ).val(),
								acstkn: jQuery( "#aquagates_payments_creditcard-acstkn" ).val(),
								usrid: jQuery( "#aquagates_payments_creditcard-userid" ).val(),
								cardnumber: jQuery( "#aquagates_payments_creditcard-card-number" ).val().replace( /　/g, "" ).replace( / /g, "" ).replace( /-/g, "" ),
								exp: jQuery( "#aquagates_payments_creditcard-card-expiry" ).val().replace( /　/g, "" ).replace( / /g, "" ),
								cvv: jQuery( "#aquagates_payments_creditcard-card-cvc" ).val().replace( /　/g, "" ).replace( / /g, "" ),
								cardname: jQuery( "#aquagates_payments_creditcard-card-username" ).val()
							}, APCC_TokenHakkou_CallBack);
						}
					}
				}
				
				function APCC_TokenHakkou_CallBack( res ){
					if( res.result === "success" ){
						jQuery( "#aquagates_payments_creditcard-onetimetoken" ).val( res.tokenResponse.token );
						jQuery( "#aquagates_payments_creditcard-onetimetokenkey" ).val( res.tokenResponse.tokenkey );
						jQuery( "#aquagates_payments_creditcard-errcode" ).val( "" );
						APCC_FormValidateFlg = true;
					}else if( res.result === "failure" ){
						jQuery( "#aquagates_payments_creditcard-errcode" ).val( res.errorCode );
						APCC_FormValidateFlg = false;
						APCC_ValidateErrShow = true;
						APCC_Validate_SetErrMsg( "トークン取得エラーが発生しました、再度お試しください。（err-gtkn）（" + res.errorCode + "）" );
					}
					APCC_SubmitButtonControll();
				}

				function APCC_Validate_Init(){
					APCC_FormValidateFlg = true;
					jQuery( "#aquagates_payments_creditcard-validate-errs" ).hide();
					jQuery( "#aquagates_payments_creditcard-validate-errs ul" ).empty();
				}
				
				function APCC_Validate_Blank_Text( selecter, label ){
					let errmsg = "";
					if( jQuery( selecter ).is( ":visible" ) ){
						let value = jQuery( selecter ).val();
						if( !value ){
							errmsg = label + " を入力してください";
							APCC_Validate_SetErrMsg( errmsg );
							APCC_FormValidateFlg = false;
						}
					}
					return errmsg;
				}
				
				function APCC_Validate_SetErrMsg( msg ){
					let selecter = jQuery( "#aquagates_payments_creditcard-validate-errs" );
					if( jQuery( selecter ).length ){
						let err = "<li>" + msg + "<li>";
						jQuery( selecter ).children( "ul" ).append( err );
						if( APCC_ValidateErrShow ){
							jQuery( selecter ).show();
						}
					}
				}
				
				function APCC_TokenClear(){
					jQuery( "#aquagates_payments_creditcard-onetimetoken" ).val("");
					jQuery( "#aquagates_payments_creditcard-onetimetokenkey" ).val("");
				}
				
				function APCC_InputCardsClear(){
					jQuery( "#aquagates_payments_creditcard-card-number" ).val( "" );
					jQuery( "#aquagates_payments_creditcard-card-expiry" ).val( "" );
					jQuery( "#aquagates_payments_creditcard-card-cvc" ).val( "" );
					jQuery( "#aquagates_payments_creditcard-card-username" ).val( "" );
				}
			</script>
		';
	}

	/**
	 * Server-side validation
	 */
	public function validate_fields(){

		$validate = true;

		$post     = $this->get_post_data();
		$token    = $post[ "aquagates_payments_creditcard-onetimetoken" ];
		$tokenkey = $post[ "aquagates_payments_creditcard-onetimetokenkey" ];
		$errcode  = $post[ "aquagates_payments_creditcard-errcode" ];

		if ( $token == "" || $tokenkey == "" ){
			$validate = false;
			wc_add_notice( "カードトークン取得エラーが発生しました、再度お試しください。（err-vali）", 'error' );
		}

		if ( $errcode != "" ){
			$validate = false;
			wc_add_notice( "エラーが発生しました、再度お試しください。（err-{$errcode}）", 'error' );
		}

		return $validate;
	}

	/**
	 * Process the payment and return the result.
	 */
	public function process_payment( $order_id ){

		$order = wc_get_order( $order_id );
		$post  = $this->get_post_data();

		$AquagatesEntity                  = new AquagatesEntity( $this );
		$AquagatesEntity->WcOrderId       = $order_id;
		$AquagatesEntity->OnetimeToken    = $post[ "aquagates_payments_creditcard-onetimetoken" ];
		$AquagatesEntity->OnetimeTokenKey = $post[ "aquagates_payments_creditcard-onetimetokenkey" ];
		$AquagatesEntity->CustomerEmail   = $AquagatesEntity->getCleanCustomerEmail( $post[ "billing_email" ] );
		$AquagatesEntity->CustomerTel     = $AquagatesEntity->getCleanCustomerTel( $post[ "billing_phone" ] );
		$AquagatesEntity->KessaiKingaku   = $AquagatesEntity->getCleanKessaiKingaku( $order->get_total() );
		$AquagatesEntity->setShiharaiHouhouType( $post[ "aquagates_payments_creditcard-card-shiharai-houhou-type" ] );

		$AquagatesService = new AquagatesService();
		$Results          = [];
		if ( $AquagatesEntity->needRequestYoshinFlg() ){
			$AquagatesEntity = $AquagatesService->Request_Yoshin_S( $AquagatesEntity );
			if ( $AquagatesEntity->ResultStatus == "success" ){
				$order->add_meta_data( 'aquagates-payments-creditcard_status', 'yoshin', true );
				$order->add_meta_data( 'aquagates-payments-creditcard_kessai_kingaku', $AquagatesEntity->KessaiKingaku, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_acceptid', $AquagatesEntity->AcceptId, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_cardtkn', $AquagatesEntity->Cardtkn, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_paymentid', $AquagatesEntity->PaymentId, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_paytime', $AquagatesEntity->PayTime, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_usrtkn', $AquagatesEntity->Usrtkn, true );
				$order->save_meta_data();
				$order->set_transaction_id( $AquagatesEntity->PaymentId );
//				$order->update_status( 'pending' );
				wc_reduce_stock_levels( $order_id );
				WC()->cart->empty_cart();
				$Results = [ 'result' => 'success', 'redirect' => $order->get_checkout_order_received_url() ];
			}else if ( $AquagatesEntity->ResultStatus == "err" ){
				$Results = [ 'result' => 'failed' ];
				wc_add_notice( $AquagatesEntity->ErrMsg, 'error' );
			}

		}else if ( $AquagatesEntity->needRequestSokujiKessaiFlg() ){
			$AquagatesEntity = $AquagatesService->Request_SokujiKessai_FromCart_S( $AquagatesEntity );
			if ( $AquagatesEntity->ResultStatus == "success" ){
				$order->add_meta_data( 'aquagates-payments-creditcard_status', 'honkessai', true );
				$order->add_meta_data( 'aquagates-payments-creditcard_kessai_kingaku', $AquagatesEntity->KessaiKingaku, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_acceptid', $AquagatesEntity->AcceptId, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_cardtkn', $AquagatesEntity->Cardtkn, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_paymentid', $AquagatesEntity->PaymentId, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_paytime', $AquagatesEntity->PayTime, true );
				$order->add_meta_data( 'aquagates-payments-creditcard_usrtkn', $AquagatesEntity->Usrtkn, true );
				$order->save_meta_data();
				$order->set_transaction_id( $AquagatesEntity->PaymentId );
//				$order->update_status( 'processing' );
				wc_reduce_stock_levels( $order_id );
				WC()->cart->empty_cart();
				$Results = [ 'result' => 'success', 'redirect' => $order->get_checkout_order_received_url() ];
			}else if ( $AquagatesEntity->ResultStatus == "err" ){
				$Results = [ 'result' => 'failed' ];
				wc_add_notice( $AquagatesEntity->ErrMsg, 'error' );
			}
		}

		return $Results;
	}

	/**
	 * Display of each button necessary for payment on the management screen
	 */
	public function aquagates_payments_creditcard_admin_order_data_after_order_details( $order ){

		if ( $order->get_payment_method() == $this->id ){
			$AquagatesEntity = new AquagatesEntity( $this );
			$status          = $order->get_meta( 'aquagates-payments-creditcard_status' );
			$status_label    = $AquagatesEntity->getPaymentsStatusLabel( $status );
			$kessaikingaku   = $order->get_meta( 'aquagates-payments-creditcard_kessai_kingaku' );
			$kessaikingaku   = $AquagatesEntity->money_Format( $kessaikingaku );
			$paymentid       = $order->get_meta( 'aquagates-payments-creditcard_paymentid' );
			$paytime         = $order->get_meta( 'aquagates-payments-creditcard_paytime' );
			$paytime         = $AquagatesEntity->date_Format( $paytime, "Y-m-d H:i:s" );

			$button_uriagekakutei_disabled   = ( $AquagatesEntity->getButtonUriageKakuteiActiveFlg( $status ) ) ? "" : "disabled";
			$button_henkincancel_disabled    = ( $AquagatesEntity->getButtonHenkinCancelActiveFlg( $status ) ) ? "" : "disabled";
			$button_sokujisaikessai_disabled = ( $AquagatesEntity->getButtonDougakuSaikessaiActiveFlg( $status ) ) ? "" : "disabled";

			$ajaxUrl     = admin_url( 'admin-ajax.php', __FILE__ );
			$nowdatetime = $AquagatesEntity->date_YMDHIS( "", "YmdHis" );

			echo '<p class="form-field form-field-wide">';
			echo '	<label>AquaGates Payments ' . esc_html( $this->title ) . ' </label>';
			echo '	<ul class="wc-order-aquagates-payments-info">';
			echo '		<li><span class="title">ステータス<span class="text">' . esc_html( $status_label ) . '</span></span></li>';
			echo '		<li><span class="title">与信/決済 済み金額<span class="text">' . esc_html( $kessaikingaku ) . '</span></span></li>';
			echo '		<li><span class="title">決済ID<span class="text">' . esc_html( $paymentid ) . '</span></span></li>';
			echo '		<li><span class="title">更新日<span class="text">' . esc_html( $paytime ) . '</span></span></li>';
			echo '		<li><span class="title">アクション</span></li>';
			echo '		<li><button type="button" class="button uriagekakutei" ' . esc_attr( $button_uriagekakutei_disabled ) . '>売上確定</button></li>';
			echo '		<li><button type="button" class="button henkincancel" ' . esc_attr( $button_henkincancel_disabled ) . '>返金・キャンセル</button></li>';
			echo '		<li><button type="button" class="button sokujisaikessai" ' . esc_attr( $button_sokujisaikessai_disabled ) . '>同額追加決済</button></li>';
			echo '	</ul>';
			echo '</p>';

			echo '
				<script>
				jQuery( document ).ready( function(){
					jQuery( "#woocommerce-order-data button.uriagekakutei").on( "click", function(){
						AquagatesPaymentsWCOrderCreditcardAction("uriagekakutei","売上確定");
					} );
					jQuery( "#woocommerce-order-data button.henkincancel").on( "click", function(){
						AquagatesPaymentsWCOrderCreditcardAction("henkincancel","返金・キャンセル");
					} );
					jQuery( "#woocommerce-order-data button.sokujisaikessai").on( "click", function(){
						AquagatesPaymentsWCOrderCreditcardAction("sokujisaikessai","同額追加決済");
					} );
				} );
				function AquagatesPaymentsWCOrderCreditcardAction( type, label ){
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
function woocommerce_payment_gateways_aquagates_payments_creditcard( $methods ){
	$methods[] = 'AquagatesPayments_Creditcard';
	return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'woocommerce_payment_gateways_aquagates_payments_creditcard' );

/**
 * Edit the available gateway to woocommerce
 */
function woocommerce_available_payment_gateways_aquagates_payments_creditcard( $methods ){
	$currency = get_woocommerce_currency();
	if ( $currency != 'JPY' ){
		unset( $methods[ 'aquagates_payments_creditcard' ] );
	}
	return $methods;
}

add_filter( 'woocommerce_available_payment_gateways', 'woocommerce_available_payment_gateways_aquagates_payments_creditcard' );
