<?php
class Df_Tweaks_Model_Settings_Banners_Left extends Df_Tweaks_Model_Settings_Banners_Abstract {
	/** @return boolean */
	public function removeFromAccount() {return $this->getYesNo('account');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/banners_left/remove_from_';}
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}