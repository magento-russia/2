<?php
class Df_Catalog_Model_Resource_Product_Option_Title_Collection extends Df_Core_Model_Resource_Collection {
	/**
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Option_Title
	 */
	public function getResource() {return Df_Catalog_Model_Resource_Product_Option_Title::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Catalog_Model_Product_Option_Title::_C;}

	const _C = __CLASS__;
}