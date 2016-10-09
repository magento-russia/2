<?php
class Df_MoySklad_Settings extends Df_Core_Model_Settings {
	/** @return Df_MoySklad_Settings_Export */
	public function export() {return Df_MoySklad_Settings_Export::s();}
	/** @return Df_MoySklad_Settings_General */
	public function general() {return Df_MoySklad_Settings_General::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}