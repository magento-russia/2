<?php
class Df_Warehousing_Model_Resource_Warehouse_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Warehousing_Model_Warehouse::mf());
	}
	/**
	 * Используется методом
	 * @see Df_Warehousing_Block_Admin_Warehouse_Index_Grid::getCollectionClass()
	 */
	const _CLASS = __CLASS__;

	/** @return Df_Warehousing_Model_Resource_Warehouse_Collection */
	public static function i() {return new self;}
}