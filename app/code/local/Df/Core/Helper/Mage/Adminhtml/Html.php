<?php
class Df_Core_Helper_Mage_Adminhtml_Html extends Mage_Core_Helper_Abstract {
	/** @return string */
	public function select() {
		return 'adminhtml/html_select';
	}

	const _CLASS = __CLASS__;
	/** @return Df_Core_Helper_Mage_Adminhtml_Html */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}