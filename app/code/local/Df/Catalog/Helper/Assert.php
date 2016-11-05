<?php
class Df_Catalog_Helper_Assert extends Mage_Core_Helper_Abstract {
	/**
	 * @var Mage_Core_Model_Resource_Abstract $resource
	 * @return Df_Catalog_Helper_Assert
	 */
	public function categoryResource(Mage_Core_Model_Resource_Abstract $resource) {
		df_assert(df_h()->catalog()->check()->categoryResource($resource));
		return $this;
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return Df_Catalog_Helper_Assert
	 */
	public function productAttributeCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df_h()->catalog()->check()->productAttributeCollection($collection));
		return $this;
	}

	/**
	 * @var Varien_Data_Collection_Db $collection
	 * @return Df_Catalog_Helper_Assert
	 */
	public function productCollection(Varien_Data_Collection_Db $collection) {
		df_assert(df_h()->catalog()->check()->productCollection($collection));
		return $this;
	}

	/**
	 * @var Mage_Core_Model_Resource_Abstract $resource
	 * @return Df_Catalog_Helper_Assert
	 */
	public function productResource(Mage_Core_Model_Resource_Abstract $resource) {
		df_assert(df_h()->catalog()->check()->productResource($resource));
		return $this;
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}