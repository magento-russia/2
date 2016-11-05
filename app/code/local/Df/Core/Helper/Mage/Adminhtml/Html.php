<?php
class Df_Core_Helper_Mage_Adminhtml_Html extends Mage_Core_Helper_Abstract {
	/** @return string */
	public function select() {
		return 'adminhtml/html_select';
	}


	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}