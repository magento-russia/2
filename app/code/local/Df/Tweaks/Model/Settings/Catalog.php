<?php
class Df_Tweaks_Model_Settings_Catalog extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Catalog_Product */
	public function product() {return Df_Tweaks_Model_Settings_Catalog_Product::s();}
	/** @return Df_Tweaks_Model_Settings_Catalog */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}