<?php
class Df_Customer_Model_Group extends Mage_Customer_Model_Group {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Customer_Model_Resource_Group::mf());
	}
	const _CLASS = __CLASS__;
	const ID__GENERAL = 1;

	/** @return Df_Customer_Model_Resource_Group_Collection */
	public static function c() {return self::s()->getCollection();}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Customer_Model_Group
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @see Df_Customer_Model_Resource_Group_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Customer_Model_Group */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}