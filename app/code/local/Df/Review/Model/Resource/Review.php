<?php
class Df_Review_Model_Resource_Review extends Mage_Review_Model_Resource_Review {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}