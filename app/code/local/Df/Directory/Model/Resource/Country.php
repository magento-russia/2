<?php
class Df_Directory_Model_Resource_Country extends Mage_Directory_Model_Mysql4_Country {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Directory_Model_Country::_construct()
	 * @see Df_Directory_Model_Resource_Country_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Directory_Model_Resource_Country */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}