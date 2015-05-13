<?php
class Df_Tweaks_Model_Settings_Paypal extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Paypal_Logo */
	public function logo() {return Df_Tweaks_Model_Settings_Paypal_Logo::s();}
	/** @return Df_Tweaks_Model_Settings_Paypal */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}