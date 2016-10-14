<?php
class Df_Tweaks_Model_Settings extends Df_Core_Model_Settings {
	/** @return Df_Tweaks_Model_Settings_Account */
	public function account() {return Df_Tweaks_Model_Settings_Account::s();}
	/** @return Df_Tweaks_Model_Settings_Remove */
	public function cart() {static $r; return $r ? $r : $r = Df_Tweaks_Model_Settings_Remove::i('cart');}
	/** @return Df_Tweaks_Model_Settings_Catalog */
	public function catalog() {return Df_Tweaks_Model_Settings_Catalog::s();}
	/** @return Df_Tweaks_Model_Settings_Footer */
	public function footer() {return Df_Tweaks_Model_Settings_Footer::s();}
	/** @return Df_Tweaks_Model_Settings_Header */
	public function header() {return Df_Tweaks_Model_Settings_Header::s();}
	/** @return Df_Tweaks_Model_Settings_Labels */
	public function labels() {return Df_Tweaks_Model_Settings_Labels::s();}
	/** @return Df_Tweaks_Model_Settings_Remove */
	public function recentlyComparedProducts() {
		static $r;
		return $r ? $r : $r = Df_Tweaks_Model_Settings_Remove::i('recently_compared_products');
	}
	/** @return Df_Tweaks_Model_Settings_Tags */
	public function tags() {return Df_Tweaks_Model_Settings_Tags::s();}
	/** @return Df_Tweaks_Model_Settings_Theme */
	public function theme() {return Df_Tweaks_Model_Settings_Theme::s();}
	/** @return Df_Tweaks_Model_Settings */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}