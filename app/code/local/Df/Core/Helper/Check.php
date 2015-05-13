<?php
class Df_Core_Helper_Check extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	public function resourceDbCollectionAbstract(Varien_Data_Collection_Db $collection) {
		return
			@class_exists('Mage_Core_Model_Resource_Db_Collection_Abstract')
			? ($collection instanceof Mage_Core_Model_Resource_Db_Collection_Abstract)
			: ($collection instanceof Mage_Core_Model_Mysql4_Collection_Abstract)
		;
	}

	/**
	 * @var int|string|null|Mage_Core_Model_Store $store
	 * @return bool
	 */
	public function storeAsParameterForGettingConfigValue($store) {
		return
				is_int($store)
			||
				is_string($store)
			||
				is_null($store)
			||
				($store instanceof Mage_Core_Model_Store)
		;
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	public function storeCollection(Varien_Data_Collection_Db $collection) {
		return
			@class_exists('Mage_Core_Model_Resource_Store_Collection')
			? ($collection instanceof Mage_Core_Model_Resource_Store_Collection)
			: ($collection instanceof Mage_Core_Model_Mysql4_Store_Collection)
		;
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	public function websiteCollection(Varien_Data_Collection_Db $collection) {
		return
			@class_exists('Mage_Core_Model_Resource_Website_Collection')
			? ($collection instanceof Mage_Core_Model_Resource_Website_Collection)
			: ($collection instanceof Mage_Core_Model_Mysql4_Website_Collection)
		;
	}

	/** @return Df_Core_Helper_Check */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}