<?php
class Df_Downloadable_Model_Resource_Link extends Mage_Downloadable_Model_Resource_Link {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}