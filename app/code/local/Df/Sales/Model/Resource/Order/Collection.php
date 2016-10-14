<?php
class Df_Sales_Model_Resource_Order_Collection extends Mage_Sales_Model_Mysql4_Order_Collection {
	/**
	 * @override
	 * @return Df_Sales_Model_Resource_Order
	 */
	public function getResource() {return Df_Sales_Model_Resource_Order::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_itemObjectClass = Df_Sales_Model_Order::_C;
	}
	/** @used-by Df_1C_Cml2_Export_Document_Orders::_construct() */
	const _C = __CLASS__;

	/** @return Df_Sales_Model_Resource_Order_Collection */
	public static function i() {return new self;}
}