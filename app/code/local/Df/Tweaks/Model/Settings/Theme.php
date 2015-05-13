<?php
class Df_Tweaks_Model_Settings_Theme extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Theme_Modern */
	public function modern() {return Df_Tweaks_Model_Settings_Theme_Modern::s();}
	/** @return Df_Tweaks_Model_Settings_Theme */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}