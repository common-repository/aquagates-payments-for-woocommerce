<?php

/**
 * A gateway that accepts requests from buttons placed on the admin
 */
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class AquagatesActionGateway{

	private function SanitizeValidatePostType( AquagatesEntity &$entity, $raw ){

		$str = sanitize_text_field( $raw );
		$str = $entity->formatter_Convert_Hankaku( $str );
		$str = $entity->formatter_Remove_Space( $str );
		$str = $entity->formatter_Remove_Trim( $str );
		if ( !isset( $str ) ){
			$str = false;
		}else if ( $str == "" ){
			$str = false;
		}else if ( !$entity->formatter_Match_English( $str ) ){
			$str = false;
		}

		if ( $str === false ){
			$entity->ResultStatus = "err";
			$entity->ErrMsg       = "不正な値が送信されました。（type）";
		}

		return $str;
	}

	private function SanitizeValidatePostOrderId( AquagatesEntity &$entity, $raw ){

		$str = sanitize_text_field( $raw );
		$str = $entity->formatter_Convert_Hankaku( $str );
		$str = $entity->formatter_Remove_Space( $str );
		$str = $entity->formatter_Remove_Trim( $str );
		if ( !isset( $str ) ){
			$str = false;
		}else if ( $str == "" ){
			$str = false;
		}else if ( !$entity->formatter_Match_Number( $str ) ){
			$str = false;
		}else if ( wc_get_order( $str ) === false ){
			$str = false;
		}

		if ( $str === false ){
			$entity->ResultStatus = "err";
			$entity->ErrMsg       = "不正な値が送信されました。（orderid）";
		}

		return $str;
	}

	private function SanitizeValidatePostNowDateTime( AquagatesEntity &$entity, $raw ){

		$str = sanitize_text_field( $raw );
		$str = $entity->formatter_Remove_Trim( $str );
		if ( !isset( $str ) ){
			$str = false;
		}else if ( $str == "" ){
			$str = false;
		}

		if ( $str === false ){
			$entity->ResultStatus = "err";
			$entity->ErrMsg       = "不正な値が送信されました。（nowdatetime）";

		}else{
			$limitdatetime = $entity->date_Interval( $str, "+ 36 hours", "YmdHis" );
			$nowdatetime   = $entity->date_YMDHIS( "", "YmdHis" );
			if ( $limitdatetime < $nowdatetime ){
				$str                  = false;
				$entity->ResultStatus = "err";
				$entity->ErrMsg       = "有効期限がきれました。画面を最新の状態に更新してから再度お試しください。（nowdatetime）";
			}
		}

		return $str;
	}

	public function Gateway_Main(){

		$AquagatesEntity = new AquagatesEntity( $this );
		$type            = $this->SanitizeValidatePostType( $AquagatesEntity, $_POST[ 'type' ] );
		$order_id        = $this->SanitizeValidatePostOrderId( $AquagatesEntity, $_POST[ 'order_id' ] );
		$this->SanitizeValidatePostNowDateTime( $AquagatesEntity, $_POST[ 'nowdatetime' ] );

		if ( $AquagatesEntity->ErrMsg == "" ){

			$AquagatesEntity->WcAjaxActionType = $type;
			$AquagatesEntity->WcOrderId        = $order_id;
			$order                             = wc_get_order( $AquagatesEntity->WcOrderId );
			$AquagatesEntity->KessaiKingaku    = $AquagatesEntity->getCleanKessaiKingaku( $order->get_total() );
			$PaymentType                       = $AquagatesEntity->getPaymentType( $order->get_payment_method() );
			$status                            = $order->get_meta( $PaymentType . '_status' );
			$AquagatesEntity->AcceptId         = $order->get_meta( $PaymentType . '_acceptid' );
			$AquagatesEntity->Cardtkn          = $order->get_meta( $PaymentType . '_cardtkn' );
			$AquagatesEntity->Usrtkn           = $order->get_meta( $PaymentType . '_usrtkn' );
			$AquagatesEntity->CustomerEmail    = $AquagatesEntity->getCleanCustomerEmail( $order->get_billing_email() );
			$AquagatesEntity->CustomerTel      = $AquagatesEntity->getCleanCustomerTel( $order->get_billing_phone() );

			$AquagatesService = new AquagatesService();

			if ( $status == "yoshin" && $AquagatesEntity->WcAjaxActionType == "creditcard_uriagekakutei" ){ //売上確定
				$AquagatesEntity = $AquagatesService->Request_UriageKakutei_S( $AquagatesEntity );
				if ( $AquagatesEntity->ResultStatus == "success" ){
					$order->add_meta_data( $PaymentType . '_status', 'honkessai', true );
					$order->add_meta_data( $PaymentType . '_kessai_kingaku', $AquagatesEntity->KessaiKingaku, true );
					$order->add_meta_data( $PaymentType . '_acceptid', $AquagatesEntity->AcceptId, true );
					//add iwasaki 20230320 与信後の売上確定はカードトークンの値がブラング返却になるのでコメントアウト
					//$order->add_meta_data( $PaymentType . '_cardtkn', $AquagatesEntity->Cardtkn, true );
					$order->add_meta_data( $PaymentType . '_paymentid', $AquagatesEntity->PaymentId, true );
					$order->add_meta_data( $PaymentType . '_paytime', $AquagatesEntity->PayTime, true );
					$order->add_meta_data( $PaymentType . '_usrtkn', $AquagatesEntity->Usrtkn, true );
					$order->save_meta_data();
					$order->set_transaction_id( $AquagatesEntity->PaymentId );
					$AquagatesEntity->SuccessMsg = "売上確定処理が完了しました。";
				}

			}else if ( in_array( $status, [ "yoshin", "honkessai" ] ) && $AquagatesEntity->WcAjaxActionType == "creditcard_henkincancel" ){ //返金・キャンセル
				$AquagatesEntity = $AquagatesService->Request_Cancel_S( $AquagatesEntity, $status );
				if ( $AquagatesEntity->ResultStatus == "success" ){
					$order->add_meta_data( $PaymentType . '_status', 'cancel', true );
					$order->add_meta_data( $PaymentType . '_kessai_kingaku', 0, true );
					$order->add_meta_data( $PaymentType . '_acceptid', $AquagatesEntity->AcceptId, true );
					$order->add_meta_data( $PaymentType . '_cardtkn', $AquagatesEntity->Cardtkn, true );
					$order->add_meta_data( $PaymentType . '_paymentid', $AquagatesEntity->PaymentId, true );
					$order->add_meta_data( $PaymentType . '_paytime', $AquagatesEntity->PayTime, true );
					$order->add_meta_data( $PaymentType . '_usrtkn', $AquagatesEntity->Usrtkn, true );
					$order->save_meta_data();
					$order->set_transaction_id( $AquagatesEntity->PaymentId );
					$AquagatesEntity->SuccessMsg = "キャンセル処理が完了しました。";
				}

			}else if ( in_array( $status, [ "honkessai", "cancel" ] ) && $AquagatesEntity->WcAjaxActionType == "creditcard_sokujisaikessai" ){ //同額追加決済
				$AquagatesEntity = $AquagatesService->Request_SokujiKessai_FromAdmin_S( $AquagatesEntity );
				if ( $AquagatesEntity->ResultStatus == "success" ){
					$order->add_meta_data( $PaymentType . '_status', 'honkessai', true );
					$order->add_meta_data( $PaymentType . '_kessai_kingaku', $AquagatesEntity->KessaiKingaku, true );
					$order->add_meta_data( $PaymentType . '_acceptid', $AquagatesEntity->AcceptId, true );
					$order->add_meta_data( $PaymentType . '_cardtkn', $AquagatesEntity->Cardtkn, true );
					$order->add_meta_data( $PaymentType . '_paymentid', $AquagatesEntity->PaymentId, true );
					$order->add_meta_data( $PaymentType . '_paytime', $AquagatesEntity->PayTime, true );
					$order->add_meta_data( $PaymentType . '_usrtkn', $AquagatesEntity->Usrtkn, true );
					$order->save_meta_data();
					$order->set_transaction_id( $AquagatesEntity->PaymentId );
					$AquagatesEntity->SuccessMsg = "売上確定処理が完了しました。";
				}

			}else{
				$AquagatesEntity->ResultStatus = "err";
				$AquagatesEntity->ErrMsg       = "処理タイプが見当たらず、エラーが発生しました。";
			}

			$msg = "";
			if ( $AquagatesEntity->ResultStatus == "success" ){
				$msg = $AquagatesEntity->SuccessMsg;
			}else if ( $AquagatesEntity->ResultStatus == "err" ){
				$msg = $AquagatesEntity->ErrMsg;
			}

			$obj               = new stdClass();
			$obj->ResultStatus = $AquagatesEntity->ResultStatus;
			$obj->Msg          = $msg;
			return json_encode( $obj, JSON_UNESCAPED_UNICODE );
		}

	}
}

