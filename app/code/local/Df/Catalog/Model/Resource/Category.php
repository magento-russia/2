<?php
class Df_Catalog_Model_Resource_Category extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category {
	/** @return Df_Catalog_Model_Resource_Category */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}