<?php
class Df_Newsletter_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Newsletter_Model_Settings_Subscription */
	public function subscription() {return Df_Newsletter_Model_Settings_Subscription::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}