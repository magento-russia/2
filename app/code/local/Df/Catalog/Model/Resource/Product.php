<?php
class Df_Catalog_Model_Resource_Product extends Mage_Catalog_Model_Resource_Product {
	/** @return Df_Catalog_Model_Resource_Product */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}