<?php
namespace Df\YandexMarket;
class Settings extends \Df_Core_Model_Settings {
	/** @return Settings\Api */
	public function api() {return Settings\Api::s();}
	/** @return Settings\Diagnostics */
	public function diagnostics() {return Settings\Diagnostics::s();}
	/** @return Settings\General */
	public function general() {return Settings\General::s();}
	/** @return Settings\Other */
	public function other() {return Settings\Other::s();}
	/** @return Settings\Products */
	public function products() {return Settings\Products::s();}
	/** @return Settings\Shop */
	public function shop() {return Settings\Shop::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}