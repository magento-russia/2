<?php
class Df_Tweaks_Model_Settings_Banners_Right extends Df_Tweaks_Model_Settings_Banners_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/banners_right/remove_from_';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}