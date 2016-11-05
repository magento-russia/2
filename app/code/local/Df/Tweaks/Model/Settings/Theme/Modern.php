<?php
class Df_Tweaks_Model_Settings_Theme_Modern extends Df_Core_Model_Settings {
	/** @return boolean */
	public function removeBottomBanner() {
		return $this->getYesNo('df_tweaks/theme_modern/remove_bottom_banner');
	}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}