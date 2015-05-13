<?php
class Df_Pickup_Model_Resource_Point_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	/** @return Df_Pickup_Model_Resource_Point_Collection */
	public function joinLocation() {
		$this->getSelect()
			->joinLeft(
				$name = array('location' => rm_table(Df_Core_Model_Resource_Location::TABLE_NAME))
				,$cond = 'main_table.location_id = location.location_id'
				,$cols = Zend_Db_Select::SQL_WILDCARD
			)
		;
		return $this;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Pickup_Model_Point::mf(), Df_Pickup_Model_Resource_Point::mf());
	}
	/**
	 * Используется методом @see Df_Pickup_Block_Admin_Point_Index_Grid::getCollectionClass()
	 */
	const _CLASS = __CLASS__;
	/** @return Df_Pickup_Model_Resource_Point_Collection */
	public static function i() {return new self;}
}