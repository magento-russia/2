<?php
/**
 * Обратите внимание, что класс @see Mage_Sales_Model_Mysql4_Order_Status
 * отсутствует в Magento CE 1.4.
 */
class Df_Sales_Model_Resource_Order_Status extends Mage_Sales_Model_Mysql4_Order_Status {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Sales_Model_Order_Status::_construct()
	 * @see Df_Sales_Model_Resource_Order_Status_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Sales_Model_Resource_Order_Status */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}