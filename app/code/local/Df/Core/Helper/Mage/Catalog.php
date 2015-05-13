<?php
class Df_Core_Helper_Mage_Catalog extends Mage_Core_Helper_Abstract {
	/** @return Df_Core_Helper_Mage_Catalog_Category */
	public function category() {;return Df_Core_Helper_Mage_Catalog_Category::s();}
	/** @return Mage_Catalog_Helper_Category */
	public function categoryHelper() {return Mage::helper('catalog/category');}
	/** @return Mage_Catalog_Model_Layer */
	public function layerSingleton() {return Mage::getSingleton('catalog/layer');}
	/** @return Df_Core_Helper_Mage_Catalog_Product */
	public function product() {;return Df_Core_Helper_Mage_Catalog_Product::s();}
	/** @return Mage_Catalog_Model_Product_Media_Config */
	public function productMediaConfig() {return Mage::getSingleton('catalog/product_media_config');}
	/** @return Mage_Catalog_Model_Session */
	public function sessionSingleton() {return Mage::getSingleton('catalog/session');}
	/** @return Df_Catalog_Model_Url */
	public function urlSingleton() {return Mage::getSingleton('catalog/url');}
	/** @return Df_Core_Helper_Mage_Catalog */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}