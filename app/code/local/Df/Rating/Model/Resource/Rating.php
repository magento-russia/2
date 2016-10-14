<?php
class Df_Rating_Model_Resource_Rating extends Mage_Rating_Model_Mysql4_Rating {
	/** @return Df_Rating_Model_Resource_Rating */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}