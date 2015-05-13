<?php
class Df_Catalog_Model_Resource_Product_Option_Title_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(
			Df_Catalog_Model_Product_Option_Title::mf()
			, Df_Catalog_Model_Resource_Product_Option_Title::mf()
		);
	}
	const _CLASS = __CLASS__;

	/** @return Df_Catalog_Model_Resource_Product_Option_Title_Collection */
	public static function i() {return new self;}
}