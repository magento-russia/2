<?php
class Df_Promotion_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Banner_Model_Settings */
	public function banners() {return Df_Banner_Model_Settings::s();}
	/** @return Df_PromoGift_Model_Settings */
	public function gifts() {return Df_PromoGift_Model_Settings::s();}
	/** @return Df_Promotion_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}