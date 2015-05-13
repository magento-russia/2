<?php
class Df_Tweaks_Model_Settings_Checkout extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Checkout_Cart */
	public function cart() {return Df_Tweaks_Model_Settings_Checkout_Cart::s();}
	/** @return Df_Tweaks_Model_Settings_Checkout */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}