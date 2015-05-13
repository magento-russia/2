<?php
class Df_Core_Model_Resource_Store_Group_Collection
	extends Mage_Core_Model_Mysql4_Store_Group_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Store_Group::mf(), Df_Core_Model_Resource_Store_Group::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Core_Model_Resource_Store_Group_Collection */
	public static function i() {return new self;}
}