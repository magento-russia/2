<?php
class Df_Directory_Helper_Finder extends Mage_Core_Helper_Abstract {
	/** @return Df_Directory_Model_Finder_CallingCode */
	public function callingCode() {
		return Df_Directory_Model_Finder_CallingCode::s();
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}