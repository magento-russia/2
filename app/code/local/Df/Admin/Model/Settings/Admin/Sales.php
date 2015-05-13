<?php
class Df_Admin_Model_Settings_Admin_Sales extends Df_Core_Model_Settings {
	/** @return Df_Admin_Model_Settings_Admin_Sales_Customers */
	public function customers() {
		return Df_Admin_Model_Settings_Admin_Sales_Customers::s();
	}
	/** @return Df_Admin_Model_Settings_Admin_Sales */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}