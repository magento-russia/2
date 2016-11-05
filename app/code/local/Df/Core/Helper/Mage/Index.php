<?php
class Df_Core_Helper_Mage_Index extends Mage_Core_Helper_Abstract {
	/** @return Mage_Index_Model_Indexer */
	public function indexer() {return Mage::getSingleton('index/indexer');}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}