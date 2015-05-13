<?php
class Df_Core_Helper_Mage_Checkout extends Mage_Core_Helper_Abstract {
	/** @return Mage_Checkout_Helper_Cart */
	public function cartHelper() {return Mage::helper('checkout/cart');}
	/** @return Mage_Checkout_Helper_Data */
	public function helper() {return df_mage()->checkoutHelper();}
	/** @return Mage_Checkout_Model_Type_Onepage */
	public function onePageSingleton() {return Mage::getSingleton('checkout/type_onepage');}
	/** @return Mage_Checkout_Helper_Url */
	public function urlHelper() {return Mage::helper('checkout/url');}
	/** @return Df_Core_Helper_Mage_Checkout */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}