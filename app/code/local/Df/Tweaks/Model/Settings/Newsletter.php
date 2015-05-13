<?php
class Df_Tweaks_Model_Settings_Newsletter extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Newsletter_Subscription */
	public function subscription() {return Df_Tweaks_Model_Settings_Newsletter_Subscription::s();}
	/** @return Df_Tweaks_Model_Settings_Newsletter */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}