<?php
class Df_Catalog_Model_Resource_Category extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Catalog_Model_Category::_construct()
	 * @see Df_Catalog_Model_Resource_Category_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Catalog_Model_Resource_Category */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}