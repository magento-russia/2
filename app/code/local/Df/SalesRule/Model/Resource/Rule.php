<?php
class Df_SalesRule_Model_Resource_Rule extends Mage_SalesRule_Model_Resource_Rule {
	/** @return Df_SalesRule_Model_Resource_Rule */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}