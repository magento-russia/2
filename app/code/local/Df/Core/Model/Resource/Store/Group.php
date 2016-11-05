<?php
class Df_Core_Model_Resource_Store_Group extends Mage_Core_Model_Resource_Store_Group {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}