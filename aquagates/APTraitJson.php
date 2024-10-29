<?php

/**
 * Helper traits for Json
 */
trait APTraitJson{

	/**
	 * json Encode
	 */
	public function json_Encode( $lst ) : string{

		if ( is_null( $lst ) ){
			$lst = [];
		}

		$json = "";
		if ( count( $lst ) > 0 ){
			$json = json_encode( $lst );

			if ( $json === false && json_last_error() !== JSON_ERROR_NONE ){
				throw new Exception( "json変換に失敗しました(en-" . json_last_error() . " " . json_last_error_msg() . ")", 1 );
			}
		}

		return $json;
	}

	/**
	 * json Decode
	 */
	public function json_Decode( string $json, bool $Err = true ) : array{

		$lst = [];
		if ( $json != "" ){
			if ( $this->json_SyntaxCheck( $json, $Err ) ){
				$lst = json_decode( $json, true );

				if ( is_null( $lst ) && json_last_error() !== JSON_ERROR_NONE ){
					if ( $Err ){
						throw new Exception( "json変換に失敗しました(de-" . json_last_error() . " " . json_last_error_msg() . ")", 1 );
					}else{
						$lst = [];
					}
				}
			}
		}

		return $lst;
	}

	/**
	 * json SyntaxCheck
	 */
	public function json_SyntaxCheck( string $json, bool $Err = true ) : bool{

		$flg = false;
		if ( is_string( $json ) ){
			if ( is_array( json_decode( $json, true ) ) ){
				if ( json_last_error() == JSON_ERROR_NONE ){
					$flg = true;
				}
			}
		}

		if ( $flg === false ){
			if ( $Err ){
				throw new Exception( "jsonチェックに失敗しました(de-" . json_last_error() . " " . json_last_error_msg() . ")", 1 );
			}
		}

		return $flg;
	}
}