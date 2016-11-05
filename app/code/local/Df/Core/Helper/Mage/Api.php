<?php
class Df_Core_Helper_Mage_Api extends Mage_Core_Helper_Abstract {
	/** @return Mage_Api_Model_Session */
	public function session() {return Mage::getSingleton('api/session');}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}