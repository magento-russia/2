<?php
class Df_Sales_Model_Resource_Order_Collection extends Mage_Sales_Model_Mysql4_Order_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Sales_Model_Order::mf(), Df_Sales_Model_Resource_Order::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Sales_Model_Resource_Order_Collection */
	public static function i() {return new self;}
}