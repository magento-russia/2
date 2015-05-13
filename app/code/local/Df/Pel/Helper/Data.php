<?php
class Df_Pel_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Pel_LibLoader */
	public function lib() {return Df_Pel_LibLoader::s();}

	/** @return Df_Pel_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}