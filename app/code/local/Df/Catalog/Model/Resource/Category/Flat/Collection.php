<?php
class Df_Catalog_Model_Resource_Category_Flat_Collection
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Catalog_Model_Category::mf(), Df_Catalog_Model_Resource_Category_Flat::mf());
	}
	const _CLASS = __CLASS__;
	/** @return Df_Catalog_Model_Resource_Category_Flat_Collection */
	public static function i() {return new self;}
}