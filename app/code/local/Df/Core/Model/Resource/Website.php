<?php
class Df_Core_Model_Resource_Website extends Mage_Core_Model_Mysql4_Website {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Core_Model_Website::_construct()
	 * @see Df_Core_Model_Resource_Website_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Core_Model_Resource_Website */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}