<?php
class Df_Customer_Model_Resource_Customer_Collection
	extends Mage_Customer_Model_Entity_Customer_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Customer_Model_Customer::mf(), Df_Customer_Model_Resource_Customer::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Customer_Model_Resource_Customer_Collection */
	public static function i() {return new self;}
}


