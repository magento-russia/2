<?php
class Df_Core_Model_Website extends Mage_Core_Model_Website {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_init(Df_Core_Model_Resource_Website::mf());
	}
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param bool $loadDefault[optional]
	 * @return Df_Core_Model_Resource_Website_Collection
	 */
	public static function c($loadDefault = false) {
		return self::s()->getCollection()->setLoadDefault($loadDefault);
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Core_Model_Website
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @see Df_Core_Model_Resource_Website_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Core_Model_Website */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}