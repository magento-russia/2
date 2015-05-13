<?php
class Df_Admin_Model_Resource_User_Collection extends Mage_Admin_Model_Mysql4_User_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Admin_Model_User::mf(), Df_Admin_Model_Resource_User::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Admin_Model_Resource_User_Collection */
	public static function i() {return new self;}
}