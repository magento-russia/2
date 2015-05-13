<?php
class Df_YandexMarket_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_YandexMarket_Model_Settings_Api */
	public function api() {return Df_YandexMarket_Model_Settings_Api::s();}
	/** @return Df_YandexMarket_Model_Settings_Diagnostics */
	public function diagnostics() {return Df_YandexMarket_Model_Settings_Diagnostics::s();}
	/** @return Df_YandexMarket_Model_Settings_General */
	public function general() {return Df_YandexMarket_Model_Settings_General::s();}
	/** @return Df_YandexMarket_Model_Settings_Other */
	public function other() {return Df_YandexMarket_Model_Settings_Other::s();}
	/** @return Df_YandexMarket_Model_Settings_Products */
	public function products() {return Df_YandexMarket_Model_Settings_Products::s();}
	/** @return Df_YandexMarket_Model_Settings_Shop */
	public function shop() {return Df_YandexMarket_Model_Settings_Shop::s();}
	/** @return Df_YandexMarket_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}