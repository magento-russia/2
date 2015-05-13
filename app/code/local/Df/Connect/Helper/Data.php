<?php
class Df_Connect_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Connect_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}