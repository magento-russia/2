<?php
class Df_Tweaks_Model_Settings_Catalog_Product extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Catalog_Product_List */
	public function _list() {return Df_Tweaks_Model_Settings_Catalog_Product_List::s();}
	/** @return Df_Tweaks_Model_Settings_Catalog_Product_View */
	public function view() {return Df_Tweaks_Model_Settings_Catalog_Product_View::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}