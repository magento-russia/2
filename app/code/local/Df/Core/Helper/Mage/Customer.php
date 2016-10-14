<?php
class Df_Core_Helper_Mage_Customer extends Mage_Core_Helper_Abstract {
	/** @return Mage_Customer_Helper_Address */
	public function addressHelper() {return Mage::helper('customer/address');}
	/** @return Df_Core_Helper_Mage_Customer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}