<?php
/**
 * Обратите внимание, что класс @see Mage_Sales_Model_Mysql4_Order_Status_Collection
 * отсутствует в Magento CE 1.4.
 */
class Df_Sales_Model_Resource_Order_Status_Collection
	extends Mage_Sales_Model_Mysql4_Order_Status_Collection {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Sales_Model_Order_Status::mf(), Df_Sales_Model_Resource_Order_Status::mf());
	}
	const _CLASS = __CLASS__;

	/** @return Df_Sales_Model_Resource_Order_Status_Collection */
	public static function i() {return new self;}
}