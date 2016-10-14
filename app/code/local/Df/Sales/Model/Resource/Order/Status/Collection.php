<?php
/**
 * Обратите внимание, что класс @see Mage_Sales_Model_Mysql4_Order_Status_Collection
 * отсутствует в Magento CE 1.4.
 */
class Df_Sales_Model_Resource_Order_Status_Collection
	extends Mage_Sales_Model_Mysql4_Order_Status_Collection {
	/**
	 * @override
	 * @return Df_Sales_Model_Resource_Order_Status
	 */
	public function getResource() {return Df_Sales_Model_Resource_Order_Status::s();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {$this->_itemObjectClass = Df_Sales_Model_Order_Status::_C;}
	const _C = __CLASS__;
}