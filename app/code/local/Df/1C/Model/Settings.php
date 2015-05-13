<?php
class Df_1C_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_1C_Model_Settings_CatalogExport */
	public function catalogExport() {return Df_1C_Model_Settings_CatalogExport::s();}
	/** @return Df_1C_Model_Settings_General */
	public function general() {return Df_1C_Model_Settings_General::s();}
	/** @return Df_1C_Model_Settings_Orders */
	public function orders() {return Df_1C_Model_Settings_Orders::s();}
	/** @return Df_1C_Model_Settings_Product */
	public function product() {return Df_1C_Model_Settings_Product::s();}
	/** @return Df_1C_Model_Settings_ReferenceLists */
	public function referenceLists() {return Df_1C_Model_Settings_ReferenceLists::s();}
	/** @return Df_1C_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}