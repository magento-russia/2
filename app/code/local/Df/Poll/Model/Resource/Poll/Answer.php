<?php
class Df_Poll_Model_Resource_Poll_Answer extends Mage_Poll_Model_Resource_Poll_Answer {
	/** @return Df_Poll_Model_Resource_Poll_Answer */
	public static function s() {static $r; return $r ? $r : $r = new self;}
} 