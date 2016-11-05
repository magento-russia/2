<?php
class Df_Catalog_Model_Resource_Category extends Mage_Catalog_Model_Resource_Category {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}