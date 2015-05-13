<?php
class Df_Sitemap_Helper_Data extends Mage_Core_Helper_Abstract {
	const _CLASS = __CLASS__;
	/** @return Df_Sitemap_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}