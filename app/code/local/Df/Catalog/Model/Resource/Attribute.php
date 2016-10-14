<?php
class Df_Catalog_Model_Resource_Attribute extends Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute {
	/** @return Df_Catalog_Model_Resource_Attribute */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}