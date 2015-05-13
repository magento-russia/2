<?php
class Df_Qiwi_Helper_Data extends Mage_Core_Helper_Data {
	const _CLASS = __CLASS__;
	/** @return Df_Qiwi_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}