<?php
class Df_Customer_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_CustomerBalance_Model_Settings */
	public function balance() {return Df_CustomerBalance_Model_Settings::s();}
	/** @return Df_Customer_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}