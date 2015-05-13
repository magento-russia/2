<?php
class Df_Core_Model_Resource_Layout extends Mage_Core_Model_Mysql4_Layout {
	const _CLASS = __CLASS__;
	/** @return Df_Core_Model_Resource_Layout */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Core_Model_Resource_Layout */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}