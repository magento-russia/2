<?php
class Df_Customer_Model_Resource_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection {
	/**
	 * @override
	 * @return Df_Customer_Model_Resource_Customer
	 */
	public function getResource() {return Df_Customer_Model_Resource_Customer::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Customer_Model_Customer::class;}

}


