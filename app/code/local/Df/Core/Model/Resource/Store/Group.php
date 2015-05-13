<?php
class Df_Core_Model_Resource_Store_Group extends Mage_Core_Model_Mysql4_Store_Group {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Core_Model_Store_Group::_construct()
	 * @see Df_Core_Model_Resource_Store_Group_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Core_Model_Resource_Store_Group */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}