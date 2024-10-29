<?php

/**
 * Aquagates Payments Module class
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/AquagatesCommon.php';

class AquagatesModule extends AquagatesCommon{

	protected function Request_Yoshin_M( AquagatesEntity $entity ) : AquagatesEntity{

		$body = [
			"clientid"      => $entity->ConfigClientId, //マーチャントID
			"acstkn"        => $entity->ConfigAcstkn, //アクセストークン
			"type"          => 'auth', //決済種別 与信
			"money"         => $entity->KessaiKingaku, //決済金額
			"email"         => $entity->CustomerEmail, //メールアドレス
			"telno"         => $entity->CustomerTel, //電話番号
			"usrid"         => $entity->ConfigUserId, //ユーザーID
			"onetimetkn"    => $entity->OnetimeToken, //ワンタイムトークン
			"onetimetknkey" => $entity->OnetimeTokenKey, //ワンタイムトークンキー
			"ordid"         => $entity->WcOrderId, //注文ID
		];

		$response = wp_remote_post( $entity->RequestUrl_ApiConnect,
		                            $this->getWpRemotePostArgs( $body ) );

		if ( is_array( $response ) ){
			$Results = json_decode( $response[ 'body' ], true );
			$Results = $entity->getSanitizeResponseData( $Results );

			if ( $Results[ 'result' ] === "success" ){
				$entity->ResultStatus = "success";
				$entity->AcceptId     = $Results[ 'acceptid' ];
				$entity->Cardtkn      = $Results[ 'cardtkn' ];
				$entity->PaymentId    = $Results[ 'paymentid' ];
				$entity->PayTime      = $Results[ 'paytime' ];
				$entity->Usrtkn       = $Results[ 'usrtkn' ];

			}else if ( $Results[ 'result' ] === "failure" ){
				$entity->ResultStatus = "err";
				$entity               = $this->setRequestErrMsg( $entity, $Results );
			}

		}else if ( get_class( $response ) == "WP_Error" ){
			$this->WPError_WpRemotePost( $entity, $response );
		}

		return $entity;
	}

	protected function Request_UriageKakutei_M( AquagatesEntity $entity ) : AquagatesEntity{

		$body = [
			"clientid" => $entity->ConfigClientId, //マーチャントID
			"acstkn"   => $entity->ConfigAcstkn, //アクセストークン
			"type"     => 'capture', //決済種別 売上確定
			"acceptid" => $entity->AcceptId, //決済処理受付ID
			"money"    => $entity->KessaiKingaku, //決済金額
			"usrid"    => $entity->ConfigUserId, //ユーザーID
			"cardtkn"  => $entity->Cardtkn, //カードトークン
			"usrtkn"   => $entity->Usrtkn, //弊社発行ユーザー識別トークン
			"ordid"    => $entity->WcOrderId, //注文ID
		];

		$response = wp_remote_post( $entity->RequestUrl_ApiConnect,
		                            $this->getWpRemotePostArgs( $body ) );

		if ( is_array( $response ) ){
			$Results = json_decode( $response[ 'body' ], true );
			$Results = $entity->getSanitizeResponseData( $Results );

			if ( $Results[ 'result' ] === "success" ){
				$entity->ResultStatus = "success";
				$entity->AcceptId     = $Results[ 'acceptid' ];
				$entity->Cardtkn      = $Results[ 'cardtkn' ];
				$entity->PaymentId    = $Results[ 'paymentid' ];
				$entity->PayTime      = $Results[ 'paytime' ];
				$entity->Usrtkn       = $Results[ 'usrtkn' ];

			}else if ( $Results[ 'result' ] === "failure" ){
				$entity->ResultStatus = "err";
				$entity               = $this->setRequestErrMsg( $entity, $Results );
			}

		}else if ( get_class( $response ) == "WP_Error" ){
			$this->WPError_WpRemotePost( $entity, $response );
		}

		return $entity;
	}

	protected function Request_SokujiKessai_FromCart_M( AquagatesEntity $entity ) : AquagatesEntity{

		$body = [
			"clientid"      => $entity->ConfigClientId, //マーチャントID
			"acstkn"        => $entity->ConfigAcstkn, //アクセストークン
			"type"          => 'payment', //決済種別 通常決済
			"money"         => $entity->KessaiKingaku, //決済金額
			"email"         => $entity->CustomerEmail, //メールアドレス
			"telno"         => $entity->CustomerTel, //電話番号
			"usrid"         => $entity->ConfigUserId, //ユーザーID
			"onetimetkn"    => $entity->OnetimeToken, //ワンタイムトークン
			"onetimetknkey" => $entity->OnetimeTokenKey, //ワンタイムトークンキー
			"ordid"         => $entity->WcOrderId, //注文ID
		];

		$response = wp_remote_post( $entity->RequestUrl_ApiConnect,
		                            $this->getWpRemotePostArgs( $body ) );

		if ( is_array( $response ) ){
			$Results = json_decode( $response[ 'body' ], true );
			$Results = $entity->getSanitizeResponseData( $Results );

			if ( $Results[ 'result' ] === "success" ){
				$entity->ResultStatus = "success";
				$entity->AcceptId     = $Results[ 'acceptid' ];
				$entity->Cardtkn      = $Results[ 'cardtkn' ];
				$entity->PaymentId    = $Results[ 'paymentid' ];
				$entity->PayTime      = $Results[ 'paytime' ];
				$entity->Usrtkn       = $Results[ 'usrtkn' ];

			}else if ( $Results[ 'result' ] === "failure" ){
				$entity->ResultStatus = "err";
				$entity               = $this->setRequestErrMsg( $entity, $Results );
			}

		}else if ( get_class( $response ) == "WP_Error" ){
			$this->WPError_WpRemotePost( $entity, $response );
		}

		return $entity;
	}

	protected function Request_Cancel_YoshinTorikeshi_M( AquagatesEntity $entity ) : AquagatesEntity{

		$body = [
			"clientid" => $entity->ConfigClientId, //マーチャントID
			"acstkn"   => $entity->ConfigAcstkn, //アクセストークン
			"type"     => 'void', //決済種別 与信取消
			"acceptid" => $entity->AcceptId, //決済処理受付ID
			"usrid"    => $entity->ConfigUserId, //ユーザーID
			"cardtkn"  => $entity->Cardtkn, //カードトークン
			"usrtkn"   => $entity->Usrtkn, //弊社発行ユーザー識別トークン
			"ordid"    => $entity->WcOrderId, //注文ID
		];

		$response = wp_remote_post( $entity->RequestUrl_ApiConnect,
		                            $this->getWpRemotePostArgs( $body ) );

		if ( is_array( $response ) ){
			$Results = json_decode( $response[ 'body' ], true );
			$Results = $entity->getSanitizeResponseData( $Results );

			if ( $Results[ 'result' ] === "success" ){
				$entity->ResultStatus = "success";
				$entity->AcceptId     = $Results[ 'acceptid' ];
				$entity->Cardtkn      = $Results[ 'cardtkn' ];
				$entity->PaymentId    = $Results[ 'paymentid' ];
				$entity->PayTime      = $Results[ 'paytime' ];
				$entity->Usrtkn       = $Results[ 'usrtkn' ];

			}else if ( $Results[ 'result' ] === "failure" ){
				$entity->ResultStatus = "err";
				$entity               = $this->setRequestErrMsg( $entity, $Results );
			}

		}else if ( get_class( $response ) == "WP_Error" ){
			$this->WPError_WpRemotePost( $entity, $response );
		}

		return $entity;
	}

	protected function Request_Cancel_Henkin_M( AquagatesEntity $entity ) : AquagatesEntity{

		$body = [
			"clientid" => $entity->ConfigClientId, //マーチャントID
			"acstkn"   => $entity->ConfigAcstkn, //アクセストークン
			"type"     => 'refund', //決済種別 返金,
			"acceptid" => $entity->AcceptId, //決済処理受付ID
			"usrid"    => $entity->ConfigUserId, //ユーザーID
			"cardtkn"  => $entity->Cardtkn, //カードトークン
			"usrtkn"   => $entity->Usrtkn, //弊社発行ユーザー識別トークン
			"ordid"    => $entity->WcOrderId, //注文ID
		];

		$response = wp_remote_post( $entity->RequestUrl_ApiConnect,
		                            $this->getWpRemotePostArgs( $body ) );

		if ( is_array( $response ) ){
			$Results = json_decode( $response[ 'body' ], true );
			$Results = $entity->getSanitizeResponseData( $Results );

			if ( $Results[ 'result' ] === "success" ){
				$entity->ResultStatus = "success";
				$entity->AcceptId     = $Results[ 'acceptid' ];
				$entity->Cardtkn      = $Results[ 'cardtkn' ];
				$entity->PaymentId    = $Results[ 'paymentid' ];
				$entity->PayTime      = $Results[ 'paytime' ];
				$entity->Usrtkn       = $Results[ 'usrtkn' ];

			}else if ( $Results[ 'result' ] === "failure" ){
				$entity->ResultStatus = "err";
				$entity               = $this->setRequestErrMsg( $entity, $Results );
			}

		}else if ( get_class( $response ) == "WP_Error" ){
			$this->WPError_WpRemotePost( $entity, $response );
		}

		return $entity;
	}

	protected function Request_SokujiKessai_FromAdmin_M( AquagatesEntity $entity ) : AquagatesEntity{

		$body = [
			"clientid" => $entity->ConfigClientId, //マーチャントID
			"acstkn"   => $entity->ConfigAcstkn, //アクセストークン
			"type"     => 'capture', //決済種別 売上確定
			"acceptid" => $entity->AcceptId, //決済処理受付ID
			"money"    => $entity->KessaiKingaku, //決済金額
			"usrid"    => $entity->ConfigUserId, //ユーザーID
			"cardtkn"  => $entity->Cardtkn, //カードトークン
			"usrtkn"   => $entity->Usrtkn, //弊社発行ユーザー識別トークン
			"ordid"    => $entity->WcOrderId, //注文ID
		];

		$response = wp_remote_post( $entity->RequestUrl_ApiConnect,
		                            $this->getWpRemotePostArgs( $body ) );

		if ( is_array( $response ) ){
			$Results = json_decode( $response[ 'body' ], true );
			$Results = $entity->getSanitizeResponseData( $Results );

			if ( $Results[ 'result' ] === "success" ){
				$entity->ResultStatus = "success";
				$entity->AcceptId     = $Results[ 'acceptid' ];
				$entity->Cardtkn      = $Results[ 'cardtkn' ];
				$entity->PaymentId    = $Results[ 'paymentid' ];
				$entity->PayTime      = $Results[ 'paytime' ];
				$entity->Usrtkn       = $Results[ 'usrtkn' ];

			}else if ( $Results[ 'result' ] === "failure" ){
				$entity->ResultStatus = "err";
				$entity               = $this->setRequestErrMsg( $entity, $Results );
			}

		}else if ( get_class( $response ) == "WP_Error" ){
			$this->WPError_WpRemotePost( $entity, $response );
		}

		return $entity;
	}

}
