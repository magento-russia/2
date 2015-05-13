<?php
class Df_Catalog_Model_Resource_Attribute extends Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute {
	const _CLASS = __CLASS__;
	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Catalog_Model_Resource_Attribute */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}