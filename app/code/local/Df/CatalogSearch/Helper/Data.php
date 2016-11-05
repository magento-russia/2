<?php
class Df_CatalogSearch_Helper_Data extends Mage_Core_Helper_Data {
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}