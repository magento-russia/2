<?php
class Df_Seo_Model_Settings_Catalog extends Df_Core_Model_Settings {
	/** @return Df_Seo_Model_Settings_Catalog_Category */
	public function category() {return Df_Seo_Model_Settings_Catalog_Category::s();}
	/** @return Df_Seo_Model_Settings_Catalog */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}