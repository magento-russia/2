<?php
namespace Df\YandexMarket;
class Settings extends \Df_Core_Model_Settings {
	/** @return \Df\YandexMarket\Settings\Api */
	public function api() {return \Df\YandexMarket\Settings\Api::s();}
	/** @return \Df\YandexMarket\Settings\Diagnostics */
	public function diagnostics() {return \Df\YandexMarket\Settings\Diagnostics::s();}
	/** @return \Df\YandexMarket\Settings\General */
	public function general() {return \Df\YandexMarket\Settings\General::s();}
	/** @return \Df\YandexMarket\Settings\Other */
	public function other() {return \Df\YandexMarket\Settings\Other::s();}
	/** @return \Df\YandexMarket\Settings\Products */
	public function products() {return \Df\YandexMarket\Settings\Products::s();}
	/** @return \Df\YandexMarket\Settings\Shop */
	public function shop() {return \Df\YandexMarket\Settings\Shop::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}