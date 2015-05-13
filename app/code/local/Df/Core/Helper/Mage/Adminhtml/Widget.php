<?php
class Df_Core_Helper_Mage_Adminhtml_Widget extends Mage_Core_Helper_Abstract {
	/** @return string */
	public function button() {
		return 'adminhtml/widget_button';
	}

	const _CLASS = __CLASS__;
	/** @return Df_Core_Helper_Mage_Adminhtml_Widget */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}