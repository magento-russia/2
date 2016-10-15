<?php
class Df_Core_Helper_Mage_Catalog_Product extends Mage_Core_Helper_Abstract {
	/** @return Mage_Catalog_Model_Product_Status */
	public function statusSingleton() {
		return Mage::getSingleton('catalog/product_status');
	}

	/** @return Mage_Catalog_Helper_Product_Url */
	public function urlHelper() {
		return Mage::helper('catalog/product_url');
	}

	/** @return Mage_Catalog_Model_Product_Visibility */
	public function visibility() {
		return Mage::getSingleton('catalog/product_visibility');
	}


	/** @return Df_Core_Helper_Mage_Catalog_Product */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}