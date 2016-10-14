<?php
class Df_Catalog_Model_Resource_Product_Flat
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Flat {
	/** @return Df_Catalog_Model_Resource_Product_Flat */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}