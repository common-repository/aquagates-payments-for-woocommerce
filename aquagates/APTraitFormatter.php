<?php

/**
 * Helper traits for each format
 */
trait APTraitFormatter{

	/**
	 * Add Zero
	 */
	public function formatter_Add_Zero( string $str, int $iNum ) : string{

		return sprintf( "%0" . $iNum . "d", $str );
	}

	/**
	 * Add Space
	 */
	public function formatter_Add_Space( string $str, int $iNum, string $side = "L" ) : string{

		$len = strlen( $str );
		for ( $i = $len ; $i < $iNum ; $i++ ){
			if ( $side == "L" ){
				$str = " " . $str;
			}else{
				$str = $str . " ";
			}
		}

		return $str;
	}

	/**
	 * Add Space Zenkaku
	 */
	public function formatter_Add_SpaceZenkaku( string $str, int $iNum, string $side = "L" ) : string{

		$len = mb_strlen( $str, "utf-8" );
		for ( $i = $len ; $i < $iNum ; $i++ ){
			if ( $side == "L" ){
				$str = "　" . $str;
			}else{
				$str = $str . "　";
			}
		}

		return $str;
	}

	/**
	 * Add Random String
	 */
	public function formatter_Add_RandomString( int $length ) : string{

		$str   = array_merge( range( 'a', 'z' ), range( '0', '9' ), range( 'A', 'Z' ) );
		$r_str = null;
		for ( $i = 0 ; $i < $length ; $i++ ){
			$r_str .= $str[ rand( 0, count( $str ) - 1 ) ];
		}

		return $r_str;
	}

	/**
	 * Add Name Keisyou
	 */
	public function formatter_Add_NameKeisyou( string $Name, string $Keisyou = "様" ) : string{

		if ( mb_substr( $Name, mb_strlen( $Name, "UTF-8" ) - 1, 1, "UTF-8" ) == $Keisyou ){
			$Name = mb_substr( $Name, 0, mb_strlen( $Name, "UTF-8" ) - 1, "UTF-8" );
		}
		$Name .= $Keisyou;

		return $Name;
	}

	/**
	 * Convert Encoding
	 */
	public function formatter_Convert_Encoding( string $str, string $to = 'UTF-8', string $from = 'SJIS' ) : string{

		return mb_convert_encoding( $str, $to, $from );
	}

	/**
	 * Convert HtmlSpecialChars
	 */
	public function formatter_Convert_HtmlSpecialChars( string $str ) : string{

		$str = $this->formatter_Convert_HtmlSpecialCharsDecode( $str );

		return htmlspecialchars( $str, ENT_NOQUOTES, 'UTF-8' );
	}

	/**
	 * Convert HtmlSpecialChars Decode
	 */
	public function formatter_Convert_HtmlSpecialCharsDecode( $str ) : string{

		if ( is_null( $str ) ){
			$str = '';
		}else{
			$str = htmlspecialchars_decode( $str, ENT_NOQUOTES );
		}

		return $str;
	}

	/**
	 * Convert HtmlEntities
	 */
	public function formatter_Convert_HtmlEntities( string $str ) : string{

		return htmlentities( $str, ENT_NOQUOTES, 'UTF-8' );
	}

	/**
	 * Convert HtmlEntity Decode
	 */
	public function formatter_Convert_HtmlEntityDecode( string $str ) : string{

		return html_entity_decode( $str, ENT_NOQUOTES, 'UTF-8' );
	}

	/**
	 * Convert Zenkaku
	 */
	public function formatter_Convert_Zenkaku( string $str ) : string{

		return mb_convert_kana( $str, "ASKV", "UTF-8" );
	}

	/**
	 * Convert Kaigyou
	 */
	public function formatter_Convert_Kaigyou( string $str ) : string{

		return preg_replace( "/(\r\n|\r)/", "\n", $str );
	}

	/**
	 * Convert CamelCase
	 */
	public function formatter_Convert_CamelCase( string $str, string $strDelimiter = "_" ) : string{

		// mypageList
		$str      = $this->formatter_Convert_PascalCase( $str, $strDelimiter );
		$str[ 0 ] = strtolower( $str[ 0 ] );

		return $str;
	}

	/**
	 * Convert PascalCase
	 */
	public function formatter_Convert_PascalCase( string $str, string $strDelimiter = "_" ) : string{

		// MypageList
		if ( preg_match( "/[a-z]+/", $str ) !== false ){
			$str = strtolower( $str );
			$str = str_replace( $strDelimiter, ' ', $str );
			$str = ucwords( $str );
			$str = str_replace( ' ', '', $str );
		}

		return $str;
	}

	/**
	 * Convert SnakeCase
	 */
	public function formatter_Convert_SnakeCase( string $str, string $strDelimiter = "_" ) : string{

		//mypage_list
		$str = preg_replace( '/([A-Z])/', $strDelimiter . '$1', $str );
		$str = strtolower( $str );

		return ltrim( $str, $strDelimiter );
	}

	/**
	 * Convert UpperCase
	 */
	public function formatter_Convert_UpperCase( string $str ) : string{

		$str = strtoupper( $str );

		return $str;
	}

	/**
	 * Convert Haifun
	 */
	public function formatter_Convert_Haifun( string $str ) : string{

		$str = str_replace( "―", "-", $str );
		$str = str_replace( "‐", "-", $str );
		$str = str_replace( "－", "-", $str );
		$str = str_replace( "–", "―", $str );

		return $str;
	}

	/**
	 * Convert ByteSize
	 */
	public function formatter_Convert_ByteSize( int $iByte ) : string{

		$str = "";
		if ( $iByte > 1000000000000 ){
			$iSize = round( $iByte / 1000000000000, 1 );
			$str   = (string) $iSize . "TB";

		}else if ( $iByte > 1000000000 ){
			$iSize = round( $iByte / 1000000000, 1 );
			$str   = (string) $iSize . "GB";

		}else if ( $iByte > 1000000 ){
			$iSize = round( $iByte / 1000000, 1 );
			$str   = (string) $iSize . "MB";

		}else{
			$iSize = round( $iByte / 1000, 1 );
			$str   = (string) $iSize . "KB";
		}

		return $str;
	}

	/**
	 * Convert URLforATag
	 */
	public function formatter_Convert_URLforATag( string $str ) : string{

		// httpの文字をリンクに変更　正規表現
		$pattern     = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/u';
		$replacement = '<a href="\1" class="link-text">\1</a>';
		$str         = preg_replace( $pattern, $replacement, $str );

		return $str;
	}

	/**
	 * Convert AndMark
	 */
	public function formatter_Convert_AndMark( string $str ) : string{

		$str = str_replace( "&", "＆", $str );

		return $str;
	}

	/**
	 * Convert HtmlTag
	 */
	public function formatter_Convert_HtmlTag( string $str, $flgKaigyou = false ) : string{

		$strReturn = "";
		for ( $i = 0 ; $i < strlen( $str ) ; $i++ ){
			if ( $str[ $i ] == "<" ){
				$strReturn .= "（";
				continue;
			}
			if ( $str[ $i ] == ">" ){
				$strReturn .= "）";
				continue;
			}
			if ( $str[ $i ] == "&" ){
				$strReturn .= "＆";
				continue;
			}
			if ( $str[ $i ] == "/" ){
				$strReturn .= "／";
				continue;
			}

			$strReturn .= $str[ $i ];
		}

		if ( !$flgKaigyou ){
			$strReturn = $this->formatter_Remove_Kaigyou( $strReturn );
		}

		return $strReturn;
	}

	/**
	 * Remove Kaigyou
	 */
	public function formatter_Remove_Kaigyou( string $str ) : string{

		return preg_replace( "/(\r\n|\r|\n)+/", "", $str );
	}

	/**
	 * Convert Ja Number
	 */
	public function formatter_Convert_JaNumber( string $str ) : string{

		$str = preg_replace_callback( '/[一二三四五六七八九十壱弐参拾百千万萬億兆〇]+/u', function( $matchs ){

			$kanji = $matchs[ 0 ];

			//全角＝半角対応表
			$kan_num = [
				'〇' => 0,
				'一' => 1,
				'二' => 2,
				'三' => 3,
				'四' => 4,
				'五' => 5,
				'六' => 6,
				'七' => 7,
				'八' => 8,
				'九' => 9,
			];
			//位取り
			$kan_deci_sub = [ '十' => 10, '百' => 100, '千' => 1000 ];
			$kan_deci     = [ '万' => 10000, '億' => 100000000, '兆' => 1000000000000, '京' => 10000000000000000 ];

			//右側から解釈していく
			$ll       = mb_strlen( $kanji );
			$a        = '';
			$deci     = 1;
			$deci_sub = 1;
			$m        = 0;
			$n        = 0;
			for ( $pos = $ll - 1 ; $pos >= 0 ; $pos-- ){
				$c = mb_substr( $kanji, $pos, 1 );
				if ( isset( $kan_num[ $c ] ) ){
					$a = $kan_num[ $c ] . $a;
				}else if ( isset( $kan_deci_sub[ $c ] ) ){
					if ( $a != '' ){
						$m = $m + $a * $deci_sub;
					}else if ( $deci_sub != 1 ){
						$m = $m + $deci_sub;
					}
					$a        = '';
					$deci_sub = $kan_deci_sub[ $c ];
				}else if ( isset( $kan_deci[ $c ] ) ){
					if ( $a != '' ){
						$m = $m + $a * $deci_sub;
					}else if ( $deci_sub != 1 ){
						$m = $m + $deci_sub;
					}
					$n        = $m * $deci + $n;
					$m        = 0;
					$a        = '';
					$deci_sub = 1;
					$deci     = $kan_deci[ $c ];
				}
			}

			$ss = '';
			if ( preg_match( "/^(0+)/", $a, $regs ) != false ){
				$ss = $regs[ 1 ];
			}
			if ( $a != '' ){
				$m = $m + $a * $deci_sub;
			}else if ( $deci_sub != 1 ){
				$m = $m + $deci_sub;
			}
			$n = $m * $deci + $n;


			if ( $ss == '' ){
				$dest = $n;
			}else if ( $n == 0 ){
				$dest = $ss;
			}else{
				$dest = $ss . $n;
			}

			return $dest;

		},                            $str );

		return $str;
	}

	/**
	 * Convert SpacesForOne
	 */
	public function formatter_Convert_SpacesForOne( string $str ) : string{

		$str = preg_replace( '/\s(?=\s)/', '', $str );
		$str = preg_replace( '/　(?=　)/', '', $str );

		return $str;
	}

	/**
	 * Convert NumberJa
	 */
	public function formatter_Convert_NumberJa( string $str ) : string{

		$str = $this->formatter_Convert_Hankaku( $str );

		$str = preg_replace_callback( '/[0-9]+\.?[0-9]*/', function( $matchs ){

			$match = $matchs[ 0 ];

			$match = str_replace( "0", "〇", $match );
			$match = str_replace( "1", "一", $match );
			$match = str_replace( "2", "二", $match );
			$match = str_replace( "3", "三", $match );
			$match = str_replace( "4", "四", $match );
			$match = str_replace( "5", "五", $match );
			$match = str_replace( "6", "六", $match );
			$match = str_replace( "7", "七", $match );
			$match = str_replace( "8", "八", $match );
			$match = str_replace( "9", "九", $match );

			if ( mb_strlen( $match ) == 2 ){
				if ( $match == "一〇" ){
					$match = "十";

				}else if ( mb_substr( $match, 0, 1 ) == "一" ){
					$match = "十" . mb_substr( $match, 1, 1 );

				}else if ( mb_substr( $match, 1, 1 ) == "〇" ){
					$match = mb_substr( $match, 0, 1 ) . "十";

				}else{
					$match = mb_substr( $match, 0, 1 ) . "十" . mb_substr( $match, 1, 1 );
				}
			}

			return $match;

		},                            $str );

		return $str;
	}

	/**
	 * Convert Hankaku
	 */
	public function formatter_Convert_Hankaku( string $str ) : string{

		return mb_convert_kana( $str, "asKV", "UTF-8" );
	}

	/**
	 * Remove Trim
	 */
	public function formatter_Remove_Trim( string $str ) : string{

		$str = preg_replace( "/^[\s　]*(.*?)[\s　]*$/u", "$1", $str );

		return $str;
	}

	/**
	 * Remove AllSymbol
	 */
	public function formatter_Remove_AllSymbol( string $str ) : string{

		$str = $this->formatter_Convert_Hankaku( $str );
		$str = $this->formatter_Remove_ASCCode( $str );
		$str = $this->formatter_Remove_Space( $str );
		$str = $this->formatter_Remove_Slash( $str );
		$str = $this->formatter_Remove_Hyphen( $str );
		$str = $this->formatter_Remove_Comma( $str );
		$str = $this->formatter_Remove_Colons( $str );
		$str = $this->formatter_Remove_DotPeriod( $str );
		$str = $this->formatter_Remove_KakkoMaru( $str );
		$str = $this->formatter_Remove_KakkoYama( $str );

		$str = $this->formatter_Remove_DateSymbol( $str );
		$str = $this->formatter_Remove_ZipSymbol( $str );
		$str = $this->formatter_Remove_MoneySymbol( $str );

		return $str;
	}

	/**
	 * Remove ASCCode
	 */
	public function formatter_Remove_ASCCode( string $str ) : string{

		$strNew = "";

		$lst = preg_split( "//u", $str, -1, PREG_SPLIT_NO_EMPTY );

		for ( $i = 0 ; $i < count( $lst ) ; $i++ ){

			if ( ord( $lst[ $i ] ) != 9 && ord( $lst[ $i ] ) != 10 && ord( $lst[ $i ] ) != 13 ){
				if ( ord( $lst[ $i ] ) > 31 ){
					$strNew = $strNew . $lst[ $i ];
				}
			}else{
				$strNew = $strNew . $lst[ $i ];
			}
		}

		return $strNew;
	}

	/**
	 * Remove Space
	 */
	public function formatter_Remove_Space( string $str ) : string{

		//http://qiita.com/a_yasui/items/fc6e1c564b5b21482882
		return preg_replace( "/(\t|\v|\f| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/", "", $str );
	}

	/**
	 * Remove Slash
	 */
	public function formatter_Remove_Slash( string $str ) : string{

		$str = str_replace( "/", "", $str );
		$str = str_replace( "／", "", $str );
		$str = str_replace( "\\", "", $str );

		return $str;
	}

	/**
	 * Remove Hyphen
	 */
	public function formatter_Remove_Hyphen( string $str ) : string{

		$str = str_replace( "ー", "", $str );
		$str = str_replace( "-", "", $str );
		$str = str_replace( "―", "", $str );
		$str = str_replace( "‐", "", $str );
		$str = str_replace( "－", "", $str );
		$str = str_replace( "–", "", $str );

		return $str;
	}

	/**
	 * Remove Comma
	 */
	public function formatter_Remove_Comma( string $str ) : string{

		$str = str_replace( ",", "", $str );

		return $str;
	}

	/**
	 * Remove Colons
	 */
	public function formatter_Remove_Colons( string $str ) : string{

		$str = str_replace( ":", "", $str );
		$str = str_replace( ";", "", $str );

		return $str;
	}

	/**
	 * Remove DotPeriod
	 */
	public function formatter_Remove_DotPeriod( string $str ) : string{

		$str = str_replace( ".", "", $str );

		return $str;
	}

	/**
	 * Remove KakkoMaru
	 */
	public function formatter_Remove_KakkoMaru( string $str ) : string{

		$str = str_replace( "（", "", $str );
		$str = str_replace( "）", "", $str );
		$str = str_replace( "(", "", $str );
		$str = str_replace( ")", "", $str );

		return $str;
	}

	/**
	 * Remove KakkoYama
	 */
	public function formatter_Remove_KakkoYama( string $str ) : string{

		$str = str_replace( "＜", "", $str );
		$str = str_replace( "＞", "", $str );
		$str = str_replace( "<", "", $str );
		$str = str_replace( ">", "", $str );

		return $str;
	}

	/**
	 * Remove DateSymbol
	 */
	public function formatter_Remove_DateSymbol( string $str ) : string{

		$str = $this->formatter_Remove_Slash( $str );
		$str = $this->formatter_Remove_Hyphen( $str );
		$str = str_replace( "年", "", $str );
		$str = str_replace( "月", "", $str );
		$str = str_replace( "日", "", $str );
		$lst = explode( "(", $str );
		$str = $lst[ 0 ];

		return $str;
	}

	/**
	 * Remove ZipSymbol
	 */
	public function formatter_Remove_ZipSymbol( string $str ) : string{

		$str = str_replace( "〒", "", $str );
		$str = $this->formatter_Remove_Hyphen( $str );

		return $str;
	}

	/**
	 * Remove MoneySymbol
	 */
	public function formatter_Remove_MoneySymbol( string $str ) : string{

		$str = str_replace( "円", "", $str );
		$str = str_replace( "$", "", $str );
		$str = str_replace( "＄", "", $str );
		$str = str_replace( "\\", "", $str );
		$str = str_replace( "￥", "", $str );
		$str = $this->formatter_Remove_Comma( $str );

		return $str;
	}

	/**
	 * Remove TelSymbol
	 */
	public function formatter_Remove_TelSymbol( string $str ) : string{

		$str = $this->formatter_Remove_ASCCode( $str );
		$str = $this->formatter_Remove_Hyphen( $str );
		$str = $this->formatter_Remove_Kakko( $str );

		return $str;
	}

	/**
	 * Remove Kakko
	 */
	public function formatter_Remove_Kakko( string $str ) : string{

		$str = str_replace( "（", "", $str );
		$str = str_replace( "）", "", $str );
		$str = str_replace( "(", "", $str );
		$str = str_replace( ")", "", $str );

		return $str;
	}

	/**
	 * Remove String
	 */
	public function formatter_Remove_String( string $str, string $strNeedle ) : string{

		$str = str_replace( $strNeedle, "", $str );

		return $str;
	}

	/**
	 * Remove ControlCode
	 */
	public function formatter_Remove_ControlCode( string $str ) : string{

		$search = [
			"\0",
			"\x01",
			"\x02",
			"\x03",
			"\x04",
			"\x05",
			"\x06",
			"\x07",
			"\x08",
			"\x0b",
			"\x0c",
			"\x0e",
			"\x0f",
			"\x10",
			"\x11",
			"\x12",
			"\x13",
			"\x14",
			"\x16",
			"\x18",
			"\x1C",
			"\x1D",
			"\x1E",
			"\x1F",
		];
		foreach ( $search as $value ){
			$str = str_replace( $value, '', $str );
		}

		return $str;
	}

	/**
	 * Remove HtmlTag
	 */
	public function formatter_Remove_HtmlTag( string $str ) : string{

		return strip_tags( $str );
	}

	/**
	 * Match String
	 */
	public function formatter_Match_String( string $str, string $strNeedle ) : bool{

		$flg = false;
		if ( !is_null( $str ) && $str != "" ){
			if ( !is_null( $strNeedle ) && $strNeedle != "" ){
				if ( mb_strpos( $str, $strNeedle ) !== false ){
					$flg = true;
				}
			}
		}

		return $flg;
	}

	/**
	 * Match ArrayValue
	 */
	public function formatter_Match_ArrayValue( array $lst, $needle ) : bool{

		$flg = false;
		if ( !is_null( $lst ) && count( $lst ) > 0 ){

			if ( !is_null( $needle ) ){

				if ( is_string( $needle ) && $needle != "" ){
					if ( in_array( $needle, $lst ) !== false ){
						$flg = true;
					}

				}else if ( is_array( $needle ) && count( $needle ) > 0 ){
					foreach ( $needle as $val ){
						if ( in_array( $val, $lst ) !== false ){
							$flg = true;
							break;
						}
					}
				}
			}
		}

		return $flg;
	}

	/**
	 * Match Email
	 */
	public function formatter_Match_Email( string $str ) : bool{

		$flg = false;
		if ( preg_match( "/^[^@]+@[^@]+$/", $str ) == 1 ){
			$flg = true;
		}
		if ( $flg ){
			$flg = false;
			$lst = explode( "@", $str );
			if ( count( $lst ) == 2 ){
				if ( getmxrr( $lst[ 1 ], $mx ) ){
					$flg = true;
				}
			}
		}

		return $flg;
	}

	/**
	 * Match Tel
	 */
	public function formatter_Match_Tel( string $str ) : bool{

		$flg = false;
		if ( preg_match( "/^[0-9]+$/", $str ) == 1 ){
			$flg = true;
		}

		return $flg;
	}

	/**
	 * Match Zip
	 */
	public function formatter_Match_Zip( string $str ) : bool{

		$flg = false;

		if ( preg_match( "/^\d{7}$/", $str ) == 1 ){
			$flg = true;
		}

		return $flg;
	}

	/**
	 * Match Image
	 */
	public function formatter_Match_Image( string $extension ) : bool{

		$extension = $this->formatter_Convert_LowerCase( $extension );

		if ( in_array( $extension, [
			"jpg",
			"jpeg",
			"gif",
			"png",
			"bmp",
		] ) ){

			return true;
		}

		return false;
	}

	/**
	 * Convert LowerCase
	 */
	public function formatter_Convert_LowerCase( string $str ) : string{

		$str = strtolower( $str );

		return $str;
	}

	/**
	 * Match Number
	 */
	public function formatter_Match_Number( string $str ) : bool{

		if ( preg_match( "/^[0-9]+$/", $str ) == 1 ){
			return true;
		}

		return false;
	}

	/**
	 * Match English
	 */
	public function formatter_Match_English( string $str ) : bool{

		if ( preg_match( "/^[a-zA-Z0-9àâäçèéêëîïôöùûüÿœ!-~\s]+$/", $str ) == 1 ){
			return true;
		}

		return false;
	}

	/**
	 * Match Katakana
	 */
	public function formatter_Match_Katakana( string $str ) : bool{

		if ( preg_match( "/^[ァ-ヶー\s]+$/u", $str ) == 1 ){
			return true;
		}

		return false;
	}

	/**
	 * Match Japanese InOne
	 */
	public function formatter_Match_Japanese_InOne( string $str ) : bool{

		foreach ( preg_split( '//u', $str, -1, PREG_SPLIT_NO_EMPTY ) as $char ){
			if ( $this->formatter_Match_Japanese( $char ) ){
				return true;
			}
		}

		return false;
	}

	/**
	 * Match Japanese
	 */
	public function formatter_Match_Japanese( string $str ) : bool{

		if ( preg_match( "/^[ぁ-んァ-ヶー一-龠、。]+$/u", $str ) == 1 ){
			return true;
		}

		return false;
	}

	/**
	 * Cut String
	 */
	public function formatter_Cut_String( string $str, string $strCutStart, string $strCutEnd = "", bool $cutstart = true ) : string{

		$iStart = 0;

		if ( mb_strpos( $str, $strCutStart ) !== false ){
			if ( $cutstart ){
				$iStart = mb_strpos( $str, $strCutStart ) + 1;
			}else{
				$iStart = mb_strpos( $str, $strCutStart );
			}
			if ( $strCutEnd == "" ){
				$str = mb_substr( $str, $iStart, null, 'UTF-8' );
			}else{
				$iEnd = mb_strpos( $str, $strCutEnd );
				$str  = mb_substr( $str, $iStart, $iEnd - $iStart, 'UTF-8' );
			}
		}

		$str = $this->formatter_Convert_Trim( $str );

		return $str;
	}

	/**
	 * Convert Trim
	 */
	public function formatter_Convert_Trim( string $str ) : string{

		$str = trim( $str );

		return $str;
	}

	/**
	 * Cut Number
	 */
	public function formatter_Cut_Number( string $str, int $iCutStart, int $iCutLength = null ) : string{

		if ( is_null( $iCutLength ) ){
			$iCutLength = mb_strlen( $str );
		}

		$str = mb_substr( $str, $iCutStart, $iCutLength );

		$str = $this->formatter_Convert_Trim( $str );

		return $str;
	}

	/**
	 * Convert Boolean
	 */
	protected function formatter_Convert_Boolean( string $val, string $true = "", string $false = "" ){

		$bool = false;
		if ( $val ){
			$bool = true;
		}

		if ( $true != "" && $false != "" ){
			$bool = ( $bool ) ? $true : $false;
		}

		return $bool;
	}


}