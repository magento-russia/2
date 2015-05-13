<?php
class Df_Admin_Model_Role extends Mage_Admin_Model_Role {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Admin_Model_Resource_Role::mf());
	}
	const _CLASS = __CLASS__;
	/** @return Df_Admin_Model_Resource_Role_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Admin_Model_Role
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @see Df_Admin_Model_Resource_Role_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Admin_Model_Role */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}