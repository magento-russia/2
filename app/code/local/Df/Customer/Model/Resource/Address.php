<?php
class Df_Customer_Model_Resource_Address extends Mage_Customer_Model_Entity_Address {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Customer_Model_Address::_construct()
	 * @see Df_Customer_Model_Resource_Address_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Customer_Model_Resource_Address */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}