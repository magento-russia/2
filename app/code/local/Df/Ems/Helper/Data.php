<?php
class Df_Ems_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return $this */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}