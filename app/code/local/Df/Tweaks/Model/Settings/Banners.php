<?php
class Df_Tweaks_Model_Settings_Banners extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Banners_Left */
	public function left() {return Df_Tweaks_Model_Settings_Banners_Left::s();}
	/** @return Df_Tweaks_Model_Settings_Banners_Right */
	public function right() {return Df_Tweaks_Model_Settings_Banners_Right::s();}
	/** @return Df_Tweaks_Model_Settings_Banners */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}