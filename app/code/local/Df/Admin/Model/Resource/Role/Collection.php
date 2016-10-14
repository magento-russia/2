<?php
class Df_Admin_Model_Resource_Role_Collection extends Mage_Admin_Model_Mysql4_Role_Collection {
	/**
	 * @override
	 * @return Df_Admin_Model_Resource_Role
	 */
	public function getResource() {return Df_Admin_Model_Resource_Role::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Admin_Model_Role::_C;}
}