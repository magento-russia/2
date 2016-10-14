<?php
class Df_Core_Model_Resource_Store_Group_Collection extends Mage_Core_Model_Mysql4_Store_Group_Collection {
	/**
	 * @override
	 * @return Df_Core_Model_Resource_Store_Group
	 */
	public function getResource() {return Df_Core_Model_Resource_Store_Group::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_itemObjectClass = Df_Core_Model_Store_Group::_C;
	}
	const _C = __CLASS__;
}