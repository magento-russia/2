<?php
class Df_Admin_Model_Resource_Role extends Mage_Admin_Model_Mysql4_Role {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Admin_Model_Role::_construct()
	 * @see Df_Admin_Model_Resource_Role_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Admin_Model_Resource_Role */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}