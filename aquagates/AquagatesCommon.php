<?php

/**
 * Aquagates Payments Common class
 */
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class AquagatesCommon{

	protected function ServiceException( AquagatesEntity $entity, \Throwable $e ) : void{

		$Msg = "";

		if ( !is_null( $e->getPrevious() ) ){
			if ( !$entity->formatter_Match_String( $e->getFile(), "vendor" ) ){
				$e = $e->getPrevious();
			}
		}
		$Msg .= "[Type] : " . get_class( $e ) . "\n";
		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ){
			$Msg .= "[Code] : " . $e->getCode() . "\n";
			$Msg .= "[File] : " . $e->getFile() . "\n";
			$Msg .= "[Line] : " . $e->getLine() . "\n";
		}
		$Msg .= "[Msg] : " . $e->getMessage() . "\n";

		$entity->ResultStatus = "err";
		$entity->ErrMsg       = $Msg;
	}

	protected function getWpRemotePostArgs( array $body ){

		$args = [
			'body'        => $body,
			'timeout'     => '120',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => [],
			'cookies'     => [],
		];

		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ){
			$args[ 'sslverify' ] = false;
		}

		return $args;
	}

	protected function WPError_WpRemotePost( AquagatesEntity $entity, WP_Error $err ){

		$str = "レスポンスエラー({$entity->RequestType})(HttpCode {$err->get_error_code()})";

		throw new \Exception( $str, 1 );
	}

	public function setRequestErrMsg( AquagatesEntity $entity, $Results ) : AquagatesEntity{

		if ( isset( $Results[ 'errorCode' ] ) ){
			$ErrorCode      = $Results[ 'errorCode' ];
			$ErrorMsg       = $entity->getRequestErrMsg( $Results[ 'errorCode' ] );
			$entity->ErrMsg = "カード決済処理エラーが発生しました。" . $ErrorMsg . "({$entity->RequestType})({$ErrorCode})";
		}else{
			$entity->ErrMsg = "カード決済処理エラーが発生しました。({$entity->RequestType})";
		}

		return $entity;
	}
}
