<?php
class Df_Speed_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Speed_Model_Settings_BlockCaching */
	public function blockCaching() {return Df_Speed_Model_Settings_BlockCaching::s();}
	/** @return Df_Speed_Model_Settings_General */
	public function general() {return Df_Speed_Model_Settings_General::s();}
	/** @return Df_Speed_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}