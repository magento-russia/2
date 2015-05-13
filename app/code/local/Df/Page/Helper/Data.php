<?php
class Df_Page_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Page_Helper_Head */
	public function head() {return Df_Page_Helper_Head::s();}
	/** @return Df_Page_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}