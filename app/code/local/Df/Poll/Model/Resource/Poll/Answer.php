<?php
class Df_Poll_Model_Resource_Poll_Answer extends Mage_Poll_Model_Mysql4_Poll_Answer {
	const _CLASS = __CLASS__;	
	/** @return string */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf_r(__CLASS__);}
	/** @return Df_Poll_Model_Resource_Poll_Answer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
} 