<?php
class Df_Customer_Model_Resource_Group extends Mage_Customer_Model_Entity_Group {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Customer_Model_Group::_construct()
	 * @see Df_Customer_Model_Resource_Group_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Customer_Model_Resource_Group */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}