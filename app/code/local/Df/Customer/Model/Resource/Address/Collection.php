<?php
class Df_Customer_Model_Resource_Address_Collection extends Mage_Customer_Model_Entity_Address_Collection {
	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Address
	 */
	public function getResource() {return Df_Customer_Model_Resource_Address::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Customer_Model_Address::_C;}
	const _C = __CLASS__;
}