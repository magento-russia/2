<?php
class Df_Ems_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Ems_Helper_Api */
	public function api() {
		return Df_Ems_Helper_Api::s();
	}

	/** @return Df_Ems_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}