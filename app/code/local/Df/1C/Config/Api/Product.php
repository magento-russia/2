<?php
class Df_1C_Config_Api_Product extends Df_1C_Config_Api_Cml2 {
	/** @return Df_1C_Config_Api_Product_Description */
	public function description() {return Df_1C_Config_Api_Product_Description::s();}
	/** @return Df_1C_Config_Api_Product_Name */
	public function name() {return Df_1C_Config_Api_Product_Name::s();}
	/** @return Df_1C_Config_Api_Product_Other */
	public function other() {return Df_1C_Config_Api_Product_Other::s();}
	/** @return Df_1C_Config_Api_Product_Prices */
	public function prices() {return Df_1C_Config_Api_Product_Prices::s();}
	/** @return Df_1C_Config_Api_Product */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}