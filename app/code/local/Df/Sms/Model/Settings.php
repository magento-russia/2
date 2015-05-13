<?php
class Df_Sms_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Sms_Model_Settings_General */
	public function general() {return Df_Sms_Model_Settings_General::s();}
	/** @return Df_Sms_Model_Settings_Sms16Ru */
	public function sms16ru() {return Df_Sms_Model_Settings_Sms16Ru::s();}
	/** @return Df_Sms_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}