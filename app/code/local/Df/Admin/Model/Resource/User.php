<?php
class Df_Admin_Model_Resource_User extends Mage_Admin_Model_Mysql4_User {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Admin_Model_User::_construct()
	 * @see Df_Admin_Model_Resource_User_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Admin_Model_Resource_User */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}