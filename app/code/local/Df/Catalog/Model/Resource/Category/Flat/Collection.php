<?php
class Df_Catalog_Model_Resource_Category_Flat_Collection
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat_Collection {
	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Category_Flat
	 */
	public function getResource() {return Df_Catalog_Model_Resource_Category_Flat::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Catalog_Model_Category::_C;}

	const _C = __CLASS__;
}