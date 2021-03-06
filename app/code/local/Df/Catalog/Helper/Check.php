<?php
class Df_Catalog_Helper_Check extends Mage_Core_Helper_Abstract {
	/**
	 * @var Varien_Data_Collection_Db $c
	 * @return bool
	 */
	public function categoryCollection(Varien_Data_Collection_Db $c) {
		return
				@class_exists('Mage_Catalog_Model_Resource_Category_Collection')
			?
					($c instanceof Mage_Catalog_Model_Resource_Category_Collection)
				||
					($c instanceof Mage_Catalog_Model_Resource_Category_Flat_Collection)
			:
					($c instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection)
				||
					($c instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection)
		;
	}

	/**
	 * @var Mage_Core_Model_Resource_Abstract $resource
	 * @return bool
	 */
	public function categoryResource(Mage_Core_Model_Resource_Abstract $resource) {
		return
			@class_exists('Mage_Catalog_Model_Resource_Category')
			? ($resource instanceof Mage_Catalog_Model_Resource_Category)
			: ($resource instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Category)
		;
	}

	/**
	 * @var Varien_Data_Collection_Db $c
	 * @return bool
	 */
	public function productAttributeCollection(Varien_Data_Collection_Db $c) {
		return
			@class_exists('Mage_Catalog_Model_Resource_Product_Attribute_Collection')
			? ($c instanceof Mage_Catalog_Model_Resource_Product_Attribute_Collection)
			: ($c instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection)
		;
	}

	/**
	 * @var Varien_Data_Collection_Db $c
	 * @return bool
	 */
	public function productCollection(Varien_Data_Collection_Db $c) {
		return
			@class_exists('Mage_Catalog_Model_Resource_Product_Collection')
			? ($c instanceof Mage_Catalog_Model_Resource_Product_Collection)
			: ($c instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection)
		;
	}

	/**
	 * @var Mage_Core_Model_Resource_Abstract $resource
	 * @return bool
	 */
	public function productResource(Mage_Core_Model_Resource_Abstract $resource) {
		return
			@class_exists('Mage_Catalog_Model_Resource_Product')
			? ($resource instanceof Mage_Catalog_Model_Resource_Product)
			: ($resource instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Product)
		;
	}

	/**
	 * @var Varien_Data_Collection_Db $c
	 * @return bool
	 */
	public function productTypeConfigurableAttributeCollection(Varien_Data_Collection_Db $c) {
		return
			@class_exists('Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection')
			? ($c instanceof Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection)
			: ($c instanceof Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Type_Configurable_Attribute_Collection)
		;
	}

	/** @return Df_Catalog_Helper_Check */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}