<?php
class Df_Core_Model_Resource_Website extends Mage_Core_Model_Resource_Website {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}