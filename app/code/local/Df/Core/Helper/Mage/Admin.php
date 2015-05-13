<?php
class Df_Core_Helper_Mage_Admin extends Mage_Core_Helper_Abstract {
	/** @return Mage_Admin_Model_Session */
	public function session() {
		return Mage::getSingleton('admin/session');
	}

	/** @return Df_Core_Helper_Mage_Admin */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}