<?php
class Df_Poll_Model_Resource_Poll extends Mage_Poll_Model_Mysql4_Poll {
	const _CLASS = __CLASS__;	
	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Poll_Model_Resource_Poll */
	public static function s() {static $r; return $r ? $r : $r = new self;}
} 