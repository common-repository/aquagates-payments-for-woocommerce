<?php

/**
 * Aquagates Payments Service class
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/AquagatesModule.php';

class AquagatesService extends AquagatesModule{

	public function Request_Yoshin_S( AquagatesEntity $entity ) : AquagatesEntity{

		try{
			$entity->RequestType = "与信";
			$entity              = $this->Request_Yoshin_M( $entity );

		}catch( \Throwable $e ){
			$this->ServiceException( $entity, $e );
		}

		return $entity;
	}

	public function Request_UriageKakutei_S( AquagatesEntity $entity ) : AquagatesEntity{

		try{
			$entity->RequestType = "売上確定";
			$entity              = $this->Request_UriageKakutei_M( $entity );

		}catch( \Throwable $e ){
			$this->ServiceException( $entity, $e );
		}

		return $entity;
	}

	public function Request_SokujiKessai_FromCart_S( AquagatesEntity $entity ) : AquagatesEntity{

		try{
			$entity->RequestType = "即時決済";
			$entity              = $this->Request_SokujiKessai_FromCart_M( $entity );

		}catch( \Throwable $e ){
			$this->ServiceException( $entity, $e );
		}

		return $entity;
	}

	public function Request_Cancel_S( AquagatesEntity $entity, $status ) : AquagatesEntity{

		try{
			$entity->RequestType = "";

			if ( $status == "yoshin" ){
				$entity->RequestType = "キャンセル-与信取消";
				$entity              = $this->Request_Cancel_YoshinTorikeshi_M( $entity );

			}else if ( $status == "honkessai" ){
				$entity->RequestType = "キャンセル-返金";
				$entity              = $this->Request_Cancel_Henkin_M( $entity );
			}

		}catch( \Throwable $e ){
			$this->ServiceException( $entity, $e );
		}

		return $entity;
	}

	public function Request_SokujiKessai_FromAdmin_S( AquagatesEntity $entity ) : AquagatesEntity{

		try{
			$entity->RequestType = "同額追加決済";
			$entity              = $this->Request_SokujiKessai_FromAdmin_M( $entity );

		}catch( \Throwable $e ){
			$this->ServiceException( $entity, $e );
		}

		return $entity;
	}

}
