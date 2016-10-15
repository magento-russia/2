<?php
class Df_Admin_Model_Resource_User_Collection extends Mage_Admin_Model_Mysql4_User_Collection {
	/**
	 * @override
	 * @return Df_Admin_Model_Resource_User
	 */
	public function getResource() {return Df_Admin_Model_Resource_User::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Admin_Model_User::class;}
}