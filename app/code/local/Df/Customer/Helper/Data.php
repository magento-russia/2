<?php
class Df_Customer_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Customer_Helper_Assert */
	public function assert() {return Df_Customer_Helper_Assert::s();}

	/** @return Df_Customer_Helper_Check */
	public function check() {return Df_Customer_Helper_Check::s();}

	/** @return Df_Customer_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}