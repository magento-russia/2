<?php
class Df_Catalog_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Catalog_Model_Settings_Navigation */
	public function navigation() {
		return Df_Catalog_Model_Settings_Navigation::s();
	}
	/** @return Df_Catalog_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}