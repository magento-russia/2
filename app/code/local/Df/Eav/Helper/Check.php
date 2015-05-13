<?php
class Df_Eav_Helper_Check extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	public function entityAttributeCollection(Varien_Data_Collection_Db $collection) {
		return
			@class_exists('Mage_Eav_Model_Resource_Entity_Attribute_Collection')
			? ($collection instanceof Mage_Eav_Model_Resource_Entity_Attribute_Collection)
			: ($collection instanceof Mage_Eav_Model_Mysql4_Entity_Attribute_Collection)
		;
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	public function entityAttributeOptionCollection(Varien_Data_Collection_Db $collection) {
		return
			@class_exists('Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection')
			? ($collection instanceof Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection)
			: ($collection instanceof Mage_Eav_Model_Mysql4_Entity_Attribute_Option_Collection)
		;
	}

	/** @return Df_Eav_Helper_Check */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}