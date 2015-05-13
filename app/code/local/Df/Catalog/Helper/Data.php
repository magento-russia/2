<?php
class Df_Catalog_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Catalog_Helper_Assert */
	public function assert() {return Df_Catalog_Helper_Assert::s();}
	/** @return Df_Catalog_Helper_Check */
	public function check() {return Df_Catalog_Helper_Check::s();}
	/** @return Df_Catalog_Helper_Product */
	public function product() {return Df_Catalog_Helper_Product::s();}
	/** @return Df_Catalog_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}