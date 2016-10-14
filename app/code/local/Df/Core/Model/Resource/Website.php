<?php
class Df_Core_Model_Resource_Website extends Mage_Core_Model_Mysql4_Website {
	/** @return Df_Core_Model_Resource_Website */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}