<?php
class Df_Core_Model_Layout_Data extends Mage_Core_Model_Layout_Data {
	const _CLASS = __CLASS__;
	/** @return Df_Core_Model_Layout_Data */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return Df_Core_Model_Layout_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}