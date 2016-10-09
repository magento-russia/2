<?php
// 2016-10-09
class Df_MoySklad_Settings_Export extends Df_Core_Model_Settings {
	/** @return Df_MoySklad_Settings_Export_Products */
	public function products() {return Df_MoySklad_Settings_Export_Products::s();}
	/** @return Df_MoySklad_Settings_Export */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}