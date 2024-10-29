<?php

/**
 * Helper traits for dates
 */
trait APTraitDate{

	/**
	 * Date validity check
	 */
	public function date_Check( string $date ) : bool{

		$date = $this->formatter_Remove_DateSymbol( $date );

		if ( is_string( $date ) ){
			if ( $date == "" || $date == "0000-00-00" || $date == "00000000" ){
				return false;
			}
			$date = $this->date_Split( $date );
		}

		if ( is_array( $date ) === false ){
			return false;

		}else if ( count( $date ) != 3 ){
			return false;

		}else if ( !array_key_exists( "Y", $date ) || !array_key_exists( "M", $date ) || !array_key_exists( "D", $date ) ){
			return false;
		}

		return checkdate( $date[ 'M' ], $date[ 'D' ], $date[ 'Y' ] );
	}

	/**
	 * split date
	 */
	public function date_Split( string $date ) : array{

		$date = $this->formatter_Remove_DateSymbol( $date );

		$lst = [];
		if ( strlen( $date ) == 8 ){
			$lst[ 'Y' ] = $date[ 0 ] . $date[ 1 ] . $date[ 2 ] . $date[ 3 ];
			$lst[ 'M' ] = $date[ 4 ] . $date[ 5 ];
			$lst[ 'D' ] = $date[ 6 ] . $date[ 7 ];

		}else if ( strlen( $date ) == 6 ){
			$lst[ 'Y' ] = $date[ 0 ] . $date[ 1 ] . $date[ 2 ] . $date[ 3 ];
			$lst[ 'M' ] = $date[ 4 ] . $date[ 5 ];
			$lst[ 'D' ] = "01";

		}else if ( strlen( $date ) == 14 || strlen( $date ) == 17 || strlen( $date ) == 19 ){
			$lst[ 'Y' ] = $date[ 0 ] . $date[ 1 ] . $date[ 2 ] . $date[ 3 ];
			$lst[ 'M' ] = $date[ 4 ] . $date[ 5 ];
			$lst[ 'D' ] = $date[ 6 ] . $date[ 7 ];
		}

		return $lst;
	}

	/**
	 * Interval calculations for dates
	 */
	public function date_Interval( string $date, string $interval, string $format = "Y-m-d" ) : string{

		$date = $this->formatter_Remove_DateSymbol( $date );

		return $this->date_YMD( $date . " " . $interval, $format );
	}

	/**
	 * Get year of date
	 */
	public function date_Get_Y( string $date ) : string{

		$date = $this->formatter_Remove_DateSymbol( $date );

		return $date[ 0 ] . $date[ 1 ] . $date[ 2 ] . $date[ 3 ];
	}

	/**
	 * Get month of date
	 */
	public function date_Get_M( string $date ) : string{

		$date = $this->formatter_Remove_DateSymbol( $date );

		return $date[ 4 ] . $date[ 5 ];
	}

	/**
	 * Get day of date
	 */
	public function date_Get_D( string $date ) : string{

		$date = $this->formatter_Remove_DateSymbol( $date );

		return $date[ 6 ] . $date[ 7 ];
	}

	/**
	 * get year
	 */
	public function date_Y( string $time = "", string $format = "Y" ) : string{

		if ( $time == "" ){
			return date( $format );
		}else{
			return date( $format, strtotime( $time ) );
		}
	}

	/**
	 * get year and month
	 */
	public function date_YM( string $time = "", string $format = "Y-m" ) : string{

		if ( $time == "" ){
			return date( $format );
		}else{
			return date( $format, strtotime( $time ) );
		}
	}

	/**
	 * get year, month and day
	 */
	public function date_YMD( string $time = "", string $format = "Y-m-d" ) : string{

		if ( $this->formatter_Match_String( $format, "(w)" ) ){

			$format = str_replace( "(w)", "", $format );
			$date   = ( $time == "" ) ? date( $format ) : date( $format, strtotime( $time ) );
			$date   .= "(" . $this->date_Youbi( $time ) . ")";

			return $date;

		}else{
			return ( $time == "" ) ? date( $format ) : date( $format, strtotime( $time ) );
		}
	}

	/**
	 * get time
	 */
	public function date_HIS( string $time = "" ) : string{

		$hi = ( $time == "" ) ? date( "H:i" ) : date( "H:i", strtotime( $time ) );

		return $hi . ":00";
	}

	/**
	 * get time
	 */
	public function date_YMDHIS( string $time = "", string $format = "Y-m-d H:i:s" ) : string{

		return $this->date_YMD( $time, $format );
	}

	/**
	 * Get Timestamp
	 */
	public function date_Youbi( string $time = "" ) : string{

		$lst = [ "日", "月", "火", "水", "木", "金", "土" ];
		if ( $time == "" ){
			return $lst[ date( "w" ) ];
		}else{
			return $lst[ date( "w", strtotime( $time ) ) ];
		}
	}

	/**
	 * Change date format
	 */
	public function date_Format( ?string $date, string $format = "Y-m-d" ) : string{

		if ( is_null( $date ) || $date == "0000-00-00" || $date == "0000-00-00 00:00:00" ){
			$date = "";
		}

		if ( $this->date_Check( $date ) ){
			$str = $this->date_YMD( $date, $format );
		}else{
			$str = date( $format, strtotime( $date ) );
		}

		return $str;
	}

	/**
	 * Check if dates are equal
	 */
	public function date_Equal( string $date1, string $date2 ) : bool{

		$date1 = $this->formatter_Remove_DateSymbol( $date1 );
		$date2 = $this->formatter_Remove_DateSymbol( $date2 );

		$flg = false;

		if ( $date1 == $date2 ){
			$flg = true;
		}

		return $flg;
	}

	/**
	 * Get difference between dates
	 */
	public function date_Diff( string $now, string $date ){

		$now  = $this->formatter_Remove_DateSymbol( $now );
		$date = $this->formatter_Remove_DateSymbol( $date );

		return ( strtotime( $date ) - strtotime( $now ) ) / ( 60 * 60 * 24 );
	}

}