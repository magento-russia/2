<?php
class Df_Rating_Model_Resource_Rating extends Mage_Rating_Model_Mysql4_Rating {
	const _CLASS = __CLASS__;
	/**
	 * @see Df_Rating_Model_Rating::_construct()
	 * @see Df_Rating_Model_Resource_Rating_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Rating_Model_Resource_Rating */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}