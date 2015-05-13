<?php
class Df_Catalog_Model_Resource_Product_Option
	extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option {
	const _CLASS = __CLASS__;

	/**
	 * @see Df_Catalog_Model_Product_Option::_construct()
	 * @see Df_Catalog_Model_Resource_Product_Option_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Catalog_Model_Resource_Product_Option */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}