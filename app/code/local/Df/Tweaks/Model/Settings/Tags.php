<?php
class Df_Tweaks_Model_Settings_Tags extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Tags_Popular */
	public function popular() {return Df_Tweaks_Model_Settings_Tags_Popular::s();}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}