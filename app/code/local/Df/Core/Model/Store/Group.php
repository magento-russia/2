<?php
class Df_Core_Model_Store_Group extends Mage_Core_Model_Store_Group {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Resource_Store_Group::mf());
	}
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param bool $loadDefault[optional]
	 * @return Df_Core_Model_Resource_Store_Group_Collection
	 */
	public static function c($loadDefault = false) {
		return self::s()->getCollection()->setLoadDefault($loadDefault);
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Store_Group
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @see Df_Core_Model_Resource_Store_Group_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Core_Model_Store_Group */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}