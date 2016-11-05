<?php
class Df_Catalog_Model_Resource_Product_Option extends Mage_Catalog_Model_Resource_Product_Option {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}