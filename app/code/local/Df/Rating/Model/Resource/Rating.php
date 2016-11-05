<?php
class Df_Rating_Model_Resource_Rating extends Mage_Rating_Model_Resource_Rating {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}