<?php
class Df_1C_Config_Api_Product_Name extends Df_1C_Config_Api_Cml2 {
	/** @return string */
	public function getSource() {return $this->getString('df_1c/product__name/source');}
	/** @return Df_1C_Config_Api_Product_Name */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}