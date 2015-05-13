<?php
class Df_Core_Helper_Units extends Mage_Core_Helper_Data {
	/** @return Df_Core_Model_Units_Length */
	public function length() {return Df_Core_Model_Units_Length::s();}
	/** @return Df_Core_Model_Units_Weight */
	public function weight() {return Df_Core_Model_Units_Weight::s();}
	/** @return Df_Core_Helper_Units */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}