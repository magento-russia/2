<?php
class Df_Catalog_Model_Product_Type_Configurable extends Mage_Catalog_Model_Product_Type_Configurable {
	/** @return Df_Catalog_Model_Product_Type_Configurable */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}