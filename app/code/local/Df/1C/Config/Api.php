<?php
class Df_1C_Config_Api extends Df_Core_Model_Settings {
	/** @return Df_1C_Config_Api_CatalogExport */
	public function catalogExport() {return Df_1C_Config_Api_CatalogExport::s();}
	/** @return Df_1C_Config_Api_General */
	public function general() {return Df_1C_Config_Api_General::s();}
	/** @return Df_1C_Config_Api_Orders */
	public function orders() {return Df_1C_Config_Api_Orders::s();}
	/** @return Df_1C_Config_Api_Product */
	public function product() {return Df_1C_Config_Api_Product::s();}
	/** @return Df_1C_Config_Api_ReferenceLists */
	public function referenceLists() {return Df_1C_Config_Api_ReferenceLists::s();}
	/** @return Df_1C_Config_Api */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}