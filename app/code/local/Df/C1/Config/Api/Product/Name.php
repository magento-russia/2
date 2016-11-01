<?php
class Df_C1_Config_Api_Product_Name extends Df_C1_Config_Api_Cml2 {
	/** @return string */
	public function getSource() {return $this->v('df_1c/product__name/source');}
	/** @return Df_C1_Config_Api_Product_Name */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}