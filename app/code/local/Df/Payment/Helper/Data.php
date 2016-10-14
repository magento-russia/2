<?php
class Df_Payment_Helper_Data extends Mage_Payment_Helper_Data {
	/** @return Df_Payment_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}