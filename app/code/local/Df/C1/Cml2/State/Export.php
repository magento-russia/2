<?php
class Df_C1_Cml2_State_Export extends Df_Core_Model {
	/** @return Df_C1_Cml2_State_Export_Products */
	public function getProducts() {return Df_C1_Cml2_State_Export_Products::s();}

	/** @return Df_C1_Cml2_State_Export */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}