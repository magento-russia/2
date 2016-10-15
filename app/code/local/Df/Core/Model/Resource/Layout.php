<?php
class Df_Core_Model_Resource_Layout extends Mage_Core_Model_Resource_Layout {
	/** @return Df_Core_Model_Resource_Layout */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}