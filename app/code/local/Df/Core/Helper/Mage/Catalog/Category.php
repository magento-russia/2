<?php
class Df_Core_Helper_Mage_Catalog_Category extends Mage_Core_Helper_Abstract {
	/** @return Mage_Catalog_Helper_Category_Flat */
	public function flatHelper() {return Mage::helper('catalog/category_flat');}

	const _C = __CLASS__;
	/** @return Df_Core_Helper_Mage_Catalog_Category */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}