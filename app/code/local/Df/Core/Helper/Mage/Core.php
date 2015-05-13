<?php
class Df_Core_Helper_Mage_Core extends Mage_Core_Helper_Abstract {
	/**
	 * Обратите внимание, что класс Mage_Core_Helper_Cookie
	 * отсутствует в устаревших версиях Magento,
	 * поэтому пользоваться методом надо так:
	 *
		if (@class_exists('Mage_Core_Helper_Cookie')) {
			$result[]= df_mage()->core()->cookieHelper()->isUserNotAllowSaveCookie();
		}
	 * @return Mage_Core_Helper_Cookie
	 */
	public function cookieHelper() {return Mage::helper('core/cookie');}
	/** @return Mage_Core_Model_Cookie */
	public function cookieSingleton() {return Mage::getSingleton('core/cookie');}
	/** @return Df_Core_Helper_Mage_Core_Design */
	public function design() {return Df_Core_Helper_Mage_Core_Design::s();}
	/** @return Mage_Core_Helper_Http */
	public function httpHelper() {return Mage::helper('core/http');}
	/** @return Mage_Core_Model_Layout */
	public function layoutSingleton() {return Mage::getSingleton('core/layout');}
	/** @return Mage_Core_Model_Locale */
	public function localeSingleton() {return Mage::getSingleton('core/locale');}
	/** @return Mage_Core_Model_Message */
	public function messageSingleton() {return Mage::getSingleton('core/message');}
	/** @return Mage_Core_Model_Resource */
	public function resource() {return Mage::getSingleton('core/resource');}
	/** @return Mage_Core_Model_Translate */
	public function translateSingleton() {return Mage::getSingleton('core/translate');}
	/** @return Mage_Core_Helper_Url */
	public function url() {return Mage::helper('core/url');}
	/** @return Mage_Core_Model_Url */
	public function urlSingleton() {return Mage::getSingleton('core/url');}
	/** @return Df_Core_Helper_Mage_Core */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}