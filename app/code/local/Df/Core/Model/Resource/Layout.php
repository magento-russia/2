<?php
class Df_Core_Model_Resource_Layout extends Mage_Core_Model_Resource_Layout {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}