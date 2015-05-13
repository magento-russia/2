<?php
class Df_Customer_Model_Resource_Address_Collection
	extends Mage_Customer_Model_Entity_Address_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Customer_Model_Address::mf(), Df_Customer_Model_Resource_Address::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Customer_Model_Resource_Address_Collection */
	public static function i() {return new self;}
}