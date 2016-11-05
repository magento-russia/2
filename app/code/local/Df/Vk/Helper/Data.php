<?php
class Df_Vk_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Vk_Model_Settings */
	public function settings() {return Df_Vk_Model_Settings::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}