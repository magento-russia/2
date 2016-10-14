<?php
class Df_Poll_Model_Resource_Poll extends Mage_Poll_Model_Mysql4_Poll {
	/** @return Df_Poll_Model_Resource_Poll */
	public static function s() {static $r; return $r ? $r : $r = new self;}
} 