<?php
class Df_Core_Model_Resource_Store_Group extends Mage_Core_Model_Mysql4_Store_Group {
	/** @return Df_Core_Model_Resource_Store_Group */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}