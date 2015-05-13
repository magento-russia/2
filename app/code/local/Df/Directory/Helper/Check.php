<?php
class Df_Directory_Helper_Check extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	public function regionCollection(Varien_Data_Collection_Db $collection) {
		return
			@class_exists('Mage_Directory_Model_Resource_Region_Collection')
			?
					($collection instanceof	Mage_Directory_Model_Resource_Region_Collection)
				||
					($collection instanceof	Df_Directory_Model_Resource_Region_Collection)
			: ($collection instanceof Df_Directory_Model_Resource_Region_Collection)
		;
	}

	/** @return Df_Directory_Helper_Check */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}