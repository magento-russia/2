<?php
class Df_Core_Helper_Mage_Helper extends Mage_Core_Helper_Abstract {
	/** @return Mage_Customer_Helper_Data */
	public function getCustomer() {
		return Mage::helper('customer');
	}

	/** @return Df_Core_Helper_Mage_Helper */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}