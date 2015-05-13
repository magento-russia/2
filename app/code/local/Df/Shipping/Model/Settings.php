<?php
class Df_Shipping_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Shipping_Model_Settings_Message */
	public function message() {return Df_Shipping_Model_Settings_Message::s();}
	/** @return Df_Shipping_Model_Settings_Product */
	public function product() {return Df_Shipping_Model_Settings_Product::s();}
	/** @return Df_Shipping_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}