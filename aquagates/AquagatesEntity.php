<?php

/**
 * Aquagates Payments Entity class
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'aquagates/DataEntity.php';

class AquagatesEntity extends DataEntity{

	public $RequestUrl_ApiConnect          = "https://credit.aqua-gates.com/api/connect";
	public $ConfigClientId                 = "";
	public $ConfigAcstkn                   = "";
	public $ConfigUserId                   = "";
	public $ConfigSokujiSaikessaiActiveFlg = "";
	public $ConfigKessaiType               = "";
	public $RequestType                    = "";
	public $ShiharaiHouhouType             = "";
	public $OnetimeToken                   = "";
	public $OnetimeTokenKey                = "";
	public $CustomerEmail                  = "";
	public $CustomerTel                    = "";
	public $KessaiKingaku                  = 0;
	public $AcceptId                       = "";
	public $Cardtkn                        = "";
	public $PaymentId                      = "";
	public $PayTime                        = "";
	public $Usrtkn                         = "";

	public $WcOrderId        = "";
	public $WcAjaxActionType = "";

	public function __construct( $entity = null ){

		parent::__construct( $entity );

		$this->ConfigClientId = get_option( "aquagates_payments_main_client_id" );
		$this->ConfigAcstkn   = get_option( "aquagates_payments_main_acstkn" );
		$this->ConfigUserId   = get_option( "aquagates_payments_main_user_id" );
		if ( get_option( "aquagates_payments_main_test_mode_flg" ) == "on" ){
			$this->ConfigClientId = get_option( "aquagates_payments_main_client_id_test" );
			$this->ConfigAcstkn   = get_option( "aquagates_payments_main_acstkn_test" );
			$this->ConfigUserId   = get_option( "aquagates_payments_main_user_id_test" );
		}
		$this->ConfigSokujiSaikessaiActiveFlg = ( isset( $entity->sokuji_saikessai_active_flg ) ) ? $entity->sokuji_saikessai_active_flg : "";
		$this->ConfigKessaiType               = ( isset( $entity->kessai_type ) ) ? $entity->kessai_type : "";
	}

	public function setShiharaiHouhouType( $Type = null ) : void{

		if ( !isset( $Type ) || is_null( $Type ) || $Type == "" ){
			$this->ShiharaiHouhouType = "ikkai";
		}else{
			$this->ShiharaiHouhouType = $Type;
		}
	}

	public function getShiharaiHouhouLabel() : string{

		$Label = "";
		switch ( $this->ShiharaiHouhouType ){
			case "ikkai":
				$Label = "1回払い";
				break;
			case "bunkatsu":
				$Label = "分割払い";
				break;
			case "bonus":
				$Label = "ボーナス1回払い";
				break;
			case "rebo":
				$Label = "リボ払い";
				break;
			case "2"  :
				$Label = "分割 2回払い";
				break;
			case "3"  :
				$Label = "分割 3回払い";
				break;
			case "5"  :
				$Label = "分割 5回払い";
				break;
			case "6"  :
				$Label = "分割 6回払い";
				break;
			case "10" :
				$Label = "分割 10回払い";
				break;
			case "12" :
				$Label = "分割 12回払い";
				break;
			case "15" :
				$Label = "分割 15回払い";
				break;
			case "18" :
				$Label = "分割 18回払い";
				break;
			case "20" :
				$Label = "分割 20回払い";
				break;
			case "24" :
				$Label = "分割 24回払い";
				break;
			case "30" :
				$Label = "分割 30回払い";
				break;
			case "36" :
				$Label = "分割 36回払い";
				break;
		}
		return $Label;
	}

	public function getCleanCustomerEmail( $str ) : string{

		$str = $this->formatter_Convert_HtmlSpecialChars( $str );
		$str = $this->formatter_Convert_Hankaku( $str );
		$str = $this->formatter_Remove_Space( $str );
		$str = $this->formatter_Remove_Kaigyou( $str );

		return $str;
	}

	public function getCleanCustomerTel( $str ) : string{

		$str = $this->formatter_Convert_HtmlSpecialChars( $str );
		$str = $this->formatter_Convert_Hankaku( $str );
		$str = $this->formatter_Remove_TelSymbol( $str );
		$str = $this->formatter_Remove_Space( $str );
		$str = $this->formatter_Remove_Kaigyou( $str );

		return $str;
	}

	public function getCleanKessaiKingaku( $str ) : string{

		$str = $this->formatter_Convert_HtmlSpecialChars( $str );
		$str = $this->formatter_Convert_Hankaku( $str );
		$str = $this->formatter_Remove_Kaigyou( $str );
		$str = $this->formatter_Remove_Space( $str );
		$str = $this->formatter_Remove_MoneySymbol( $str );

		return $str;
	}

	public function needRequestYoshinFlg() : bool{

		$flg = false;
		if ( $this->ConfigKessaiType == "yoshinnomi" ){ // 仮売り決済
			$flg = true;
		}
		return $flg;
	}

	public function needRequestSokujiKessaiFlg() : bool{

		$flg = false;
		if ( $this->ConfigKessaiType == "sokujikessai" ){ // 即時決済
			$flg = true;
		}
		return $flg;
	}

	public function getRequestErrMsg( string $Code ) : string{

		$Msg = "";
		switch ( $Code ){
			case "100001":
				$Msg = "不正なアクセスです。";
				break;
			case "100002":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100003":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100004":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100101":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100102":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100103":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100201":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100202":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100203":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100301":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100302":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100303":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100401":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100402":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100403":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100501":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100502":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100503":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100504":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100601":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100602":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100701":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100702":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100703":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "100801":
				$Msg = "パラメータにメールアドレスが含まれていません。";
				break;
			case "100802":
				$Msg = "メールアドレスの形式が間違っています。";
				break;
			case "100803":
				$Msg = "メールアドレスの形式が間違っています。";
				break;
			case "100901":
				$Msg = "不正なアクセスです。";
				break;
			case "100902":
				$Msg = "電話番号の形式が間違っています。";
				break;
			case "100903":
				$Msg = "電話番号の形式が間違っています。";
				break;
			case "101001":
				$Msg = "不正なアクセスです。";
				break;
			case "101011":
				$Msg = "パラメータ不正です。";
				break;
			case "101012":
				$Msg = "パラメータ不正です。";
				break;
			case "101013":
				$Msg = "パラメータ不正です。";
				break;
			case "101101":
				$Msg = "不正なアクセスです。";
				break;
			case "101102":
				$Msg = "カード名義の形式が間違っています";
				break;
			case "101103":
				$Msg = "カード名義が30文字を超えています";
				break;
			case "101201":
				$Msg = "パラメータに有効期限が含まれていません";
				break;
			case "101202":
				$Msg = "有効期限の形式が間違っています。";
				break;
			case "101203":
				$Msg = "有効期限が切れています。";
				break;
			case "101301":
				$Msg = "パラメータにセキュリティコードが含まれていません";
				break;
			case "101302":
				$Msg = "セキュリティコードの形式が間違っています";
				break;
			case "101401":
				$Msg = "カード番号の形式が間違っています。";
				break;
			case "101402":
				$Msg = "カード番号の形式が間違っています。";
				break;
			case "101403":
				$Msg = "不正なカード番号です";
				break;
			case "101404":
				$Msg = "クレジットカード情報が間違っているか利用可能なクレジットカードではありません。 大変お手数では御座いますが、再度ご確認してご記入するか、別のクレジットカードで決済をやり直して下さい。または、クレジットカード会社へお問い合わせ下さい。";
				break;
			case "101501":
				$Msg = "クレジットカード情報が間違っているか利用可能なクレジットカードではありません。 大変お手数では御座いますが、再度ご確認してご記入するか、別のクレジットカードで決済をやり直して下さい。または、クレジットカード会社へお問い合わせ下さい。";
				break;
			case "101502":
				$Msg = "現在ご利用可能なカードではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "101601":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101602":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101603":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101604":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101605":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101606":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101607":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101608":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101609":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101701":
				$Msg = "パラメータにクレジットカードトークンが含まれていません";
				break;
			case "101702":
				$Msg = "クレジットカードトークンの形式が間違っています";
				break;
			case "101703":
				$Msg = "不正なクレジットカードトークンです";
				break;
			case "101801":
				$Msg = "パラメータにリカーリング識別IDが含まれていません";
				break;
			case "101802":
				$Msg = "リカーリング識別IDの形式が間違っています";
				break;
			case "101803":
				$Msg = "有効なリカーリング識別IDではありません";
				break;
			case "101804":
				$Msg = "既にリカーリングは停止されています";
				break;
			case "101901":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101902":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101903":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "101904":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "102001":
				$Msg = "ワンタイムトークンキーの形式が間違っています。";
				break;
			case "102002":
				$Msg = "ワンタイムトークンの形式が間違っています。";
				break;
			case "102003":
				$Msg = "有効なワンタイムトークン（またはキー）ではありません";
				break;
			case "102004":
				$Msg = "ワンタイムトークンは有効期限切れです。";
				break;
			case "102101":
				$Msg = "不正なアクセスです。";
				break;
			case "102201":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "102202":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "102203":
				$Msg = "システムエラー。現在決済システムがご利用いただけません。 恐れ入りますが、ご利用サイトの窓口までお問い合わせください。";
				break;
			case "102301":
				$Msg = "不正なアクセスです。";
				break;
			case "102302":
				$Msg = "このURLは利用出来ません。";
				break;
			case "102303":
				$Msg = "このURLは利用出来ません。";
				break;
			case "102304":
				$Msg = "このURLは既に有効期限が切れている為、利用出来ません。";
				break;
			case "102310":
				$Msg = "不正なアクセスです。";
				break;
			case "102501":
				$Msg = "不正なアクセスです。";
				break;
			case "102502":
				$Msg = "不正なアクセスです。";
				break;
			case "102503":
				$Msg = "不正なアクセスです。";
				break;
			case "102601":
				$Msg = "不正なアクセスです。";
				break;
			case "102602":
				$Msg = "不正なアクセスです。";
				break;
			case "102701":
				$Msg = "不正なアクセスです。";
				break;
			case "102702":
				$Msg = "不正なアクセスです。";
				break;
			case "102703":
				$Msg = "不正なアクセスです。";
				break;
			case "102801":
				$Msg = "不正なアクセスです。";
				break;
			case "102802":
				$Msg = "不正なアクセスです。";
				break;
			case "102901":
				$Msg = "ユーザートークンの形式が間違っています";
				break;
			case "102902":
				$Msg = "不正なクレジットカードトークンです";
				break;
			case "220001":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "229001":
				$Msg = "【 決済処理が失敗しました 】 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "229999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "249999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "279999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "289999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "290001":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "290002":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "290003":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "290004":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "299001":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "299002":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "299004":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "299009":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "299999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "309999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "319999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "329999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "330001":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "330002":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "339001":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "339002":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "339003":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "339004":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "339999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "340001":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "341003":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "341006":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "341902":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "341911":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "342001":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "349999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "350001":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "350082":
				$Msg = "【 決済処理が失敗しました 】 入力された値が正しくありません。 正しい値を入力していただくか、他のクレジットカードでお申込みください。";
				break;
			case "359999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "360901":
				$Msg = "【 決済処理が失敗しました 】 入力された値が正しくありません。 正しい値を入力していただくか、他のクレジットカードでお申込みください。";
				break;
			case "361000":
				$Msg = "【 決済処理が失敗しました 】 入力された値が正しくありません。 正しい値を入力していただくか、他のクレジットカードでお申込みください。";
				break;
			case "363000":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "363100":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "363200":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "369999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "371000":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "371014":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "372001":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "372002":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "376011":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "376012":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "377106":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "377108":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "377712":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "377713":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378000":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378037":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378314":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378324":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378325":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378326":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378327":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378328":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378329":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378350":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378373":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "378418":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "379999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "389999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "399999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "409999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "437777":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "438888":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "439999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "447777":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "449999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "457777":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "458888":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "459999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "467777":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "469999":
				$Msg = "決済処理が失敗しました。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "497777":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "498888":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。カード会社への問い合わせが必要です。";
				break;
			case "499999":
				$Msg = "【 決済処理が失敗しました 】 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。予期せぬエラーです。サポートへご連絡ください。";
				break;
			case "501001":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "501004":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "599999":
				$Msg = "【 決済処理が失敗しました 】 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "600001":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "600002":
				$Msg = "【 決済処理が失敗しました 】 このカードはお取扱いできません。 他のクレジットカードまたはお支払い方法でお申込みください。";
				break;
			case "600003":
				$Msg = "【 決済処理が失敗しました 】 連続して決済することはできません。 大変お手数では御座いますが、しばらく経ってから再度アクセスしなおして下さい。";
				break;
			case "699999":
				$Msg = "【 決済処理が失敗しました 】 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "911001":
				$Msg = "スピード決済が利用可能な状態ではありません";
				break;
			case "911002":
				$Msg = "スピード決済でカード情報が取得出来なかった";
				break;
			case "930001":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "940001":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950001":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950002":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950003":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950004":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950005":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950101":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950102":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950103":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950104":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950105":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950201":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950202":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950203":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950204":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "950205":
				$Msg = "お客様は現在、決済サービスをご利用することが出来ません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "960001":
				$Msg = "クレジットカード決済は現在、利用可能ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "960002":
				$Msg = "クレジットカード決済は現在、利用可能ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "970001":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "970002":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "970004":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "970005":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "971001":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "971002":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "971003":
				$Msg = "利用可能なカード番号ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "980001":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "980002":
				$Msg = "現在システムが決済可能な状態ではありません。 大変お手数では御座いますが、ファーストペンギンカスタマーサポートへご連絡ください。";
				break;
			case "999995":
				$Msg = "不正なアクセスです。";
				break;
			case "999996":
				$Msg = "システムエラーが発生しました。 大変お手数では御座いますが、しばらく経ってから再度アクセスしなおして下さい。";
				break;
			case "999997":
				$Msg = "不正なアクセスです。";
				break;
			case "999998":
				$Msg = "不正なアクセスです。";
				break;
			case "999999":
				$Msg = "システムエラーが発生しました。 大変お手数では御座いますが、しばらく経ってから再度アクセスしなおして下さい。";
				break;
		}

		return $Msg;
	}

	public function getPaymentsStatusLabel( $Status ) : string{

		$Label = "";
		switch ( $Status ){
			case "mikessai":
				$Label = "未決済";
				break;
			case "yoshin":
				$Label = "与信済み・売上確定待ち";
				break;
			case "honkessai":
				$Label = "売上確定済み";
				break;
			case "cancel":
				$Label = "返金・キャンセル済み";
				break;
		}
		return $Label;
	}

	public function getButtonUriageKakuteiActiveFlg( $Status ) : bool{
		$flg = false;
		if ( $Status == "yoshin" ){
			$flg = true;
		}
		return $flg;
	}

	public function getButtonHenkinCancelActiveFlg( $Status ) : bool{
		$flg = false;
		if ( $Status == "yoshin" || $Status == "honkessai" ){
			$flg = true;
		}
		return $flg;
	}

	public function getButtonDougakuSaikessaiActiveFlg( $Status ) : bool{
		$flg = false;
		if ( $this->ConfigSokujiSaikessaiActiveFlg == "yes" && $Status == "honkessai" ){
			$flg = true;
		}
		return $flg;
	}

	public function getLinkPayResultHookURL() : string{

		$server_https     = sanitize_text_field( $_SERVER[ 'HTTPS' ] );
		$server_http_host = sanitize_text_field( $_SERVER[ 'HTTP_HOST' ] );

		$str = "";
		$str .= empty( $server_https ) ? 'http://' : 'https://';
		$str .= $server_http_host;
		$str .= "/?aquagates-payments-link-payresulthook=yes";

		return $str;
	}

	public function getPaymentType( $payment_method ) : string{
		$str = "";
		if ( $payment_method == "aquagates_payments_creditcard" ){
			$str = "aquagates-payments-creditcard";
		}else if ( $payment_method == "aquagates_payments_link" ){
			$str = "aquagates-payments-link";
		}

		return $str;
	}

	public function getSanitizeResponseData( $Results ) : array{

		$Results[ 'result' ]    = sanitize_text_field( $Results[ 'result' ] );
		$Results[ 'acceptid' ]  = sanitize_text_field( $Results[ 'acceptid' ] );
		$Results[ 'cardtkn' ]   = sanitize_text_field( $Results[ 'cardtkn' ] );
		$Results[ 'paymentid' ] = sanitize_text_field( $Results[ 'paymentid' ] );
		$Results[ 'paytime' ]   = sanitize_text_field( $Results[ 'paytime' ] );
		$Results[ 'usrtkn' ]    = sanitize_text_field( $Results[ 'usrtkn' ] );
		$Results[ 'errorCode' ] = sanitize_text_field( $Results[ 'errorCode' ] );

		return $Results;
	}

}
