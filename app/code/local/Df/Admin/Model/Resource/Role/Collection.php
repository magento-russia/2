<?php
class Df_Admin_Model_Resource_Role_Collection extends Mage_Admin_Model_Mysql4_Role_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Admin_Model_Role::mf(), Df_Admin_Model_Resource_Role::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Admin_Model_Resource_Role_Collection */
	public static function i() {return new self;}
}