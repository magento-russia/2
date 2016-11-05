<?php
class Df_Catalog_Model_Resource_Attribute extends Mage_Catalog_Model_Resource_Attribute {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}