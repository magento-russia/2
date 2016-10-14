<?php
class Df_Downloadable_Model_Resource_Sample extends Mage_Downloadable_Model_Mysql4_Sample {
	/** @return Df_Downloadable_Model_Resource_Sample */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}