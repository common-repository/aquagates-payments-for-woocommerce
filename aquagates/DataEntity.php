<?php

/**
 * Aquagates Payments Common Entity class
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/APTraitDate.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/APTraitFormatter.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/APTraitJson.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/APTraitMoney.php';

class DataEntity{

	use APTraitFormatter;
	use APTraitDate;
	use APTraitJson;
	use APTraitMoney;

	public $SuccessMsg   = "";
	public $ErrMsg       = "";
	public $ResultStatus = "";

	public function __construct( $entity = null ){

		if ( isset( $entity ) ){
			foreach ( $entity as $key => $value ){
				if ( !property_exists( $this, $key ) ){
					$key = $this->formatter_Convert_PascalCase( $key );
				}
				if ( property_exists( $this, $key ) ){
					$this->{$key} = $value;
				}
			}
		}

		$lstDefaultProperty = get_class_vars( get_class( $this ) );
		foreach ( $lstDefaultProperty as $key => $value ){
			if ( is_array( $value ) && array_key_exists( "class", $value ) ){
				$this->{$key}   = [];
				$this->{$key}[] = new $value[ 'class' ]( $this );
			}
		}
	}


}
