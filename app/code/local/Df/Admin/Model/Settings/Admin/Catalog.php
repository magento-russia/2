<?php
class Df_Admin_Model_Settings_Admin_Catalog extends Df_Core_Model_Settings {
	/** @return Df_Admin_Model_Settings_Admin_Catalog_Product */
	public function product() {return Df_Admin_Model_Settings_Admin_Catalog_Product::s();}
	/**
	 * @used-by Df_Admin_Model_Settings_Admin::catalog()
	 * @return Df_Admin_Model_Settings_Admin_Catalog
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}