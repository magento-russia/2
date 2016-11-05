<?php
class Df_Reports_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Reports_Model_Settings_Common */
	public function common() {return Df_Reports_Model_Settings_Common::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}