<?php
class Df_Tweaks_Model_Settings_Jquery extends Df_Core_Model_Settings_Jquery {
	/**
	 * @override
	 * @return string
	 */
	public function getLoadMode() {return $this->getString('load_mode');}
	/**
	 * @override
	 * @return boolean
	 */
	public function needRemoveExtraneous() {return $this->getYesNo('remove_extraneous');}
	/**
	 * @override
	 * @return string
	 */
	protected function getKeyPrefix() {return 'df_tweaks/other/jquery_';}
	/** @return Df_Tweaks_Model_Settings_Jquery */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}