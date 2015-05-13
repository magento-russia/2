<?php
class Df_Core_Helper_Mage_Eav extends Mage_Core_Helper_Abstract {
	/** @return Mage_Eav_Model_Config */
	public function configSingleton() {return Mage::getSingleton('eav/config');}
	/** @return Df_Core_Helper_Mage_Eav */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}