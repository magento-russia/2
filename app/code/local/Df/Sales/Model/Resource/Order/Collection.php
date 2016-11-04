<?php
class Df_Sales_Model_Resource_Order_Collection extends Mage_Sales_Model_Resource_Order_Collection {
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
		$this->_itemObjectClass = Df_Sales_Model_Order::class;
	}
	/** @used-by \Df\C1\Cml2\Export\Document\Orders::_construct() */


	/** @return Df_Sales_Model_Resource_Order_Collection */
	public static function i() {return new self;}
}