<?php
class Df_Core_Helper_Mage_Adminhtml_System extends Mage_Core_Helper_Abstract {
	/** @return Mage_Adminhtml_Model_System_Store */
	public function storeSingleton() {
		return Mage::getSingleton('adminhtml/system_store');
	}
	/** @return Df_Core_Helper_Mage_Adminhtml_System */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}