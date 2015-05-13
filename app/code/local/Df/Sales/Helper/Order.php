<?php
class Df_Sales_Helper_Order extends Mage_Core_Helper_Abstract {
	/** @return Mage_Sales_Model_Order_Config */
	public function configSingleton() {return Mage::getSingleton('sales/order_config');}
	/** @return Df_Sales_Helper_Order */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}