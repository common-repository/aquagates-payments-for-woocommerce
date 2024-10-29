<?php

/**
 * Helper traits for Money
 */
trait APTraitMoney{

	/**
	 * money Format
	 */
	public function money_Format( $money, string $tani = "円" ) : string{

		if ( $money !== "" ){
			$money = $this->formatter_Remove_MoneySymbol( $money );
			$money = number_format( $money );
			$money .= $tani;
		}

		return $money;
	}
}