<?php
/**
 * Обратите внимание, что класс @see Mage_Sales_Model_Mysql4_Order_Status
 * отсутствует в Magento CE 1.4.
 * 
 * 2016-10-16
 * Magento CE 1.4 отныне не поддерживаем.
 */
class Df_Sales_Model_Resource_Order_Status extends Mage_Sales_Model_Resource_Order_Status {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}