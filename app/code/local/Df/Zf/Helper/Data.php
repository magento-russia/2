<?php
class Df_Zf_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Zf_Helper_Db */
	public function db() {return Df_Zf_Helper_Db::s();}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}